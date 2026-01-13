<?php
// api/generate_quiz_api.php
header('Content-Type: application/json');

// --- DEBUGGING ON ---
// I enabled these so if the loading gets stuck, you will see the actual error message.
// You can change these back to 0 once everything is running perfectly.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- PATH FIX ---
// This tells PHP: "Start here, go UP one level (..), find vendor/autoload.php"
require_once __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

// YOUR API KEY
$apiKey = "AIzaSyA0xasD0i3pzgnebqW6nr_UG1msZ2E-DCU"; 

try {
    if (!isset($_FILES['pdf_file'])) { throw new Exception("No file uploaded"); }

    // 1. EXTRACT TEXT
    $parser = new Parser();
    $pdf = $parser->parseFile($_FILES['pdf_file']['tmp_name']);
    $text = $pdf->getText();
    
    if (trim($text) == "") { throw new Exception("PDF is empty."); }
    $cleanText = substr($text, 0, 4000);

    // 2. PROMPT
    $userPrompt = isset($_POST['custom_prompt']) ? $_POST['custom_prompt'] : "";
    
    $prompt = "Act as a teacher. " . $userPrompt . "
    Generate 5 multiple choice questions based on the text below.
    RETURN ONLY RAW JSON. No Markdown.
    
    REQUIRED JSON STRUCTURE:
    [
      {
        \"question\": \"Question text here\",
        \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
        \"answer\": \"Exact text of the correct option\"
      }
    ]

    Text Source: " . $cleanText;

    // 3. SEND TO GOOGLE
    // Note: Ensure 'gemini-2.5-flash' is a valid model ID for your account. 
    // If it fails with 404, try 'gemini-1.5-flash' or 'gemini-2.0-flash-exp'.
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
    
    $data = [ "contents" => [ ["parts" => [["text" => $prompt]]] ] ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // SSL Fix (Dev only)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) { throw new Exception("Connection Failed: " . curl_error($ch)); }
    curl_close($ch);

    // 4. PROCESS RESPONSE
    $jsonResponse = json_decode($response, true);
    
    if (isset($jsonResponse['error'])) {
        throw new Exception("Google API Error: " . $jsonResponse['error']['message']);
    }

    $rawContent = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
    
    // --- RESTORED ORIGINAL CLEANING LOGIC ---
    $cleanJSON = str_replace(['```json', '```', "\n"], '', $rawContent);
    
    $questions = json_decode($cleanJSON, true);

    // Safety check: if json_decode returns null, the API output was likely messy
    if ($questions === null) {
        // Last ditch effort: Try to find the [ ... ] bracket content manually
        if (preg_match('/\[.*\]/s', $rawContent, $matches)) {
            $questions = json_decode($matches[0], true);
        }
    }

    if ($questions === null) {
        throw new Exception("Error parsing AI output. Please try again.");
    }
    
    echo json_encode(['success' => true, 'questions' => $questions]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>