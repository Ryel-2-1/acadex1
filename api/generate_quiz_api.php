<?php
// api/generate_quiz_api.php
header('Content-Type: application/json');

// DEBUG (you can turn off in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

// ⚠️ WARNING: in real apps, DO NOT hardcode API keys. Use env vars.
$apiKey = getenv('GOOGLE_API_KEY') ?: null;

// Try to read a simple .env file (KEY=VALUE lines) in project root if env var isn't set
if (!$apiKey) {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $parsed = @parse_ini_file($envFile, false, INI_SCANNER_RAW);
        if ($parsed && isset($parsed['GOOGLE_API_KEY'])) {
            $apiKey = $parsed['GOOGLE_API_KEY'];
        }
    }
}

if (!$apiKey) {
    throw new Exception("Missing API key: set GOOGLE_API_KEY environment variable or create a .env with GOOGLE_API_KEY.");
}


try {
    if (!isset($_FILES['pdf_file'])) {
        throw new Exception("No file uploaded");
    }

    // 0. QUESTION TYPE (mcq / open)
    $questionType = isset($_POST['question_type']) ? strtolower(trim($_POST['question_type'])) : 'mcq';
    if ($questionType !== 'mcq' && $questionType !== 'open') {
        $questionType = 'mcq';
    }

    // 1. EXTRACT TEXT FROM PDF
    $parser = new Parser();
    $pdf = $parser->parseFile($_FILES['pdf_file']['tmp_name']);
    $text = $pdf->getText();

    if (trim($text) == "") {
        throw new Exception("PDF is empty.");
    }

    // limit length so prompt isn't too huge
    $cleanText = substr($text, 0, 4000);

    // 2. PROMPT BUILDING
    $userPrompt = isset($_POST['custom_prompt']) ? $_POST['custom_prompt'] : "";

    if ($questionType === 'mcq') {
        $typeInstruction = 'Generate 5 MULTIPLE CHOICE questions with EXACTLY 4 options each.';
        $jsonInstruction = 'Each item MUST use "type": "mcq". "options" MUST contain exactly 4 strings. "answer" MUST be EXACTLY one of those option strings.';
    } else {
        $typeInstruction = 'Generate 5 OPEN-ENDED short answer questions. NO multiple-choice options.';
        $jsonInstruction = 'Each item MUST use "type": "open". "options" MUST be an empty array []. "answer" MUST be a model answer or key points string for the teacher.';
    }

    $prompt = "
Act as a teacher. $userPrompt

$typeInstruction

RETURN ONLY RAW JSON. No Markdown, no explanation, no backticks.

REQUIRED JSON STRUCTURE (ARRAY OF OBJECTS):
[
  {
    \"type\": \"mcq\" OR \"open\",
    \"question\": \"Question text here\",
    \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"], // for open-ended, this MUST be []
    \"answer\": \"Exact text of the correct option, or model answer for open-ended\"
  }
]

$jsonInstruction

TEXT SOURCE:
$cleanText
";

    // 3. SEND TO GOOGLE GEMINI
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    $data = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // DEV ONLY: disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("Connection Failed: " . curl_error($ch));
    }
    curl_close($ch);

    // 4. PROCESS RESPONSE
    $jsonResponse = json_decode($response, true);

    if (isset($jsonResponse['error'])) {
        throw new Exception("Google API Error: " . $jsonResponse['error']['message']);
    }

    if (!isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("No content returned from AI.");
    }

    $rawContent = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];

    // Clean out code fences if Gemini adds them
    $cleanJSON = str_replace(['```json', '```'], '', $rawContent);

    $questions = json_decode($cleanJSON, true);

    // If direct decode fails, try to extract [ ... ] block
    if ($questions === null) {
        if (preg_match('/\[.*\]/s', $rawContent, $matches)) {
            $questions = json_decode($matches[0], true);
        }
    }

    if ($questions === null) {
        throw new Exception("Error parsing AI output. Please try again.");
    }

    // Normalize fields
    foreach ($questions as &$q) {
        if (!isset($q['type'])) {
            $q['type'] = $questionType;
        } else {
            $q['type'] = strtolower($q['type']) === 'open' ? 'open' : 'mcq';
        }

        if (!isset($q['options']) || !is_array($q['options'])) {
            $q['options'] = ($q['type'] === 'mcq') ? [] : [];
        }
        if (!isset($q['question'])) {
            $q['question'] = '';
        }
        if (!isset($q['answer'])) {
            $q['answer'] = '';
        }
    }

    echo json_encode(['success' => true, 'questions' => $questions]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
