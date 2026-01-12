<?php
session_start();
// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    // header("Location: index.php"); // Uncomment when login is ready
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechHub - Classwork</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="https://meet.jit.si/external_api.js"></script>

<style>
    /* --- GENERAL RESET & STYLES --- */
    body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; height: 100vh; display: flex; flex-direction: column; }
    * { box-sizing: border-box; }
/* Add this to your style section if not already present */
.dropdown-item i {
    width: 20px;
    text-align: center;
    color: #5f6368;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Specific red hover for logout */
.dropdown-item[onclick*="logout"]:hover {
    background-color: #fce8e6;
    color: #d93025;
}
.dropdown-item[onclick*="logout"]:hover i {
    color: #d93025;
}
    /* --- HEADER --- */
    header { background: white; padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; flex-shrink: 0; z-index: 100; }
    .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; }
    .logo-icon { font-size: 32px; color: #1a73e8; }
    .logo-text { font-size: 24px; font-weight: 700; color: #000; }
    .nav-links { display: flex; gap: 30px; height: 100%; }
    .nav-item { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #5f6368; font-weight: 500; height: 100%; cursor: pointer; }
    .nav-item.active { color: #1a73e8; border-bottom: 3px solid #1a73e8; }
    /* Updated Profile Styles to match Dashboard */
        .profile-section { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            text-align: right; 
            width: 250px; 
            justify-content: flex-end; 
        }
        .profile-info h4 { 
            margin: 0; 
            font-size: 15px; 
            font-weight: 600; 
            color: #333; 
        }
        .profile-info span { 
            font-size: 13px; 
            color: #777; 
            display: block; 
        }
    .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff'); background-size: cover; }

    /* --- LAYOUT --- */
    .app-layout { display: flex; flex: 1; overflow: hidden; }
    .sidebar { width: 300px; background: white; display: flex; flex-direction: column; padding: 24px 0; border-right: 1px solid #e0e0e0; }
    .sidebar-item { display: flex; align-items: center; gap: 18px; padding: 12px 30px; color: #5f6368; font-weight: 500; cursor: pointer; border-radius: 0 50px 50px 0; margin-right: 10px; transition: background 0.2s; }
    .sidebar-item:hover { background: #f5f5f5; }
    .sidebar-item.active { background: #e8f0fe; color: #1a73e8; font-weight: 600; }
    .sidebar-item.active i { color: #1a73e8; }
    .sidebar-item i { width: 24px; text-align: center; font-size: 20px; }

    .main-content { flex: 1; padding: 30px 50px; overflow-y: auto; background: white; position: relative; }
    .section-view { display: none; animation: fadeIn 0.3s; }
    .section-view.active-section { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* --- STREAM BANNER & JITSI --- */
    .class-banner { height: 200px; border-radius: 8px; background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'); background-size: cover; background-position: center; position: relative; margin-bottom: 25px; }
    .class-banner-content { position: absolute; bottom: 20px; left: 25px; right: 25px; color: white; display: flex; justify-content: space-between; align-items: flex-end; }
    .class-banner-content h1 { margin: 0; font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .virtual-btn { background-color: rgba(255, 255, 255, 0.2); color: white; border: 1px solid white; padding: 10px 20px; border-radius: 4px; font-weight: 500; display: flex; align-items: center; gap: 10px; backdrop-filter: blur(5px); cursor: pointer; transition: 0.2s; }
    .virtual-btn:hover { background-color: white; color: #1a73e8; }

    /* --- CLASSWORK ITEMS --- */
    .create-wrapper { margin-bottom: 40px; position: relative; }
    .create-btn { background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 28px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .dropdown-menu { display: none; position: absolute; top: 55px; left: 0; width: 280px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 8px 0; z-index: 1000; }
    .dropdown-item { display: flex; align-items: center; gap: 20px; padding: 12px 24px; cursor: pointer; color: #3c4043; }
    .dropdown-item:hover { background: #f5f5f5; }

    .stream-item { background: white; border: 1px solid #dadce0; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; display: flex; align-items: center; gap: 20px; transition: 0.2s; }
    .stream-item:hover { box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
    .item-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0; }
    .icon-quiz { background: #1a73e8; }
    .icon-assign { background: #e37400; }
    .icon-quest { background: #a142f4; }
    .item-content { flex: 1; }
    .item-title { font-weight: 600; color: #3c4043; margin-bottom: 4px; }
    .item-meta { font-size: 0.85rem; color: #5f6368; }

    /* QUIZ CARDS */
    .quiz-question-card { border-bottom: 1px solid #eee; padding: 25px 0; }
    .q-title { font-weight: 700; margin-bottom: 10px; }
    .q-options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .q-opt { background: #fafafa; padding: 10px; border: 1px solid #eee; border-radius: 6px; }
    .q-ans { margin-top: 10px; color: #27ae60; font-weight: 600; font-size: 0.9rem; }

    /* MODALS */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 8px; width: 550px; padding: 0; box-shadow: 0 24px 38px rgba(0,0,0,0.14); }
    .modal-content.large { width: 95%; max-width: 1100px; height: auto; }
    .modal-header { padding: 15px 24px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 24px; }
    .modal-footer { padding: 16px 24px; display: flex; justify-content: flex-end; gap: 10px; }
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; font-weight: 500; margin-bottom: 8px; color: #5f6368; }
    .input-group input, textarea { width: 100%; padding: 10px; border: 1px solid #dadce0; border-radius: 4px; }
    .upload-area-small { border: 2px dashed #dadce0; padding: 20px; text-align: center; background: #f8f9fa; cursor: pointer; color: #1a73e8; font-weight: 500; }
    
    .btn { padding: 8px 24px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500; }
    .btn-cancel { background: white; color: #5f6368; border: 1px solid #dadce0; }
    .btn-go { background: #1a73e8; color: white; }

    /* JITSI CONTAINER */
    #jitsi-container { width: 100%; height: 500px; background: #000; border-radius: 4px; overflow: hidden; }

    #detailView { display: none; }
    .back-btn { background: none; border: none; cursor: pointer; font-size: 1rem; color: #5f6368; margin-bottom: 20px; display: flex; align-items: center; gap: 5px; }
</style>
</head>
<body>

   <header>
        <div class="logo-section">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
            <a href="Classwork.php" class="nav-item active"><i class="fa-solid fa-book"></i> Classes</a>
            <a href="gradebook.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Gradebook</a>
        </nav>
        <div class="profile-section" style="position: relative;">
    <div class="profile-info" onclick="toggleProfileDropdown()" style="cursor: pointer;">
        <h4>Prof. Jhomari Gandionco</h4>
        <span>Teacher <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i></span>
    </div>
    <div class="avatar" onclick="toggleProfileDropdown()" style="cursor: pointer;"></div>
    
    <div id="profileDropdown" class="dropdown-menu" style="right: 0; left: auto; top: 60px; width: 180px;">
        <div class="dropdown-item" onclick="window.location.href='profile.php'">
            <i class="fa-solid fa-user"></i> My Profile
        </div>
        <div class="dropdown-item" onclick="window.location.href='settings.php'">
            <i class="fa-solid fa-gear"></i> Settings
        </div>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
        <div class="dropdown-item" onclick="window.location.href='teacher_login.php'" style="color: #d93025;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </div>
    </div>
</div>
    </header>

    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-item" id="tab-stream" onclick="switchTab('stream')"><i class="fa-regular fa-comment-dots"></i> Stream</div>
            <div class="sidebar-item active" id="tab-classwork" onclick="switchTab('classwork')"><i class="fa-solid fa-clipboard-list"></i> Classwork</div>
            <div class="sidebar-item" id="tab-people" onclick="switchTab('people')"><i class="fa-solid fa-user-group"></i> People</div>
            <div class="sidebar-item" id="tab-marks" onclick="switchTab('marks')"><i class="fa-solid fa-chart-simple"></i> Marks</div>
        </aside>

        <main class="main-content">
            <div id="streamSection" class="section-view">
                <div class="class-banner">
                    <div class="class-banner-content">
                        <div><h1>BSIT 4-1 Programming</h1><p>Advanced Web Development</p></div>
                        <button onclick="startMeeting()" class="virtual-btn"><i class="fa-solid fa-video"></i> Virtual Class</button>
                    </div>
                </div>
                <div id="streamFeedArea">
                    <div style="text-align:center; color:#777; margin-top:30px;">Loading stream...</div>
                </div>
            </div>

            <div id="classworkSection" class="section-view active-section">
                <div class="create-wrapper">
                    <button class="create-btn" onclick="toggleDropdown()"><i class="fa-solid fa-plus"></i> Create</button>
                    <div id="createDropdown" class="dropdown-menu">
                        <div class="dropdown-item" onclick="openModal('assignModal')"><i class="fa-solid fa-file-pen"></i> Assignment</div>
                        <div class="dropdown-item" onclick="openModal('aiModal')"><i class="fa-solid fa-robot"></i> AI Quiz Generator</div>
                        <div class="dropdown-item" onclick="openModal('questModal')"><i class="fa-solid fa-question"></i> Question</div>
                    </div>
                </div>

                <div id="streamView">
                    <div id="loadingState" style="text-align:center; margin-top:50px; color:#666;">Loading classwork...</div>
                    <div id="streamItemsArea"></div>
                </div>

                <div id="detailView">
                    <button class="back-btn" onclick="closeDetail()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                    <div style="background:white; border:1px solid #e0e0e0; border-radius:8px; padding:30px;">
                        <h1 id="detTitle" style="margin:0 0 10px 0; color:#1a73e8;"></h1>
                        <div id="detMeta" style="color:#666; margin-bottom:20px;"></div>
                        <div id="detBody"></div>
                    </div>
                </div>
            </div>

            <div id="peopleSection" class="section-view">
                <h2>People</h2>
                <p>Teacher: Prof. Gandionco</p>
                <p>Students list coming soon...</p>
            </div>
            <div id="marksSection" class="section-view">
                <h2>Marks</h2>
                <p>Gradebook data loading...</p>
            </div>
        </main>
    </div>

    <div id="jitsiModal" class="modal-overlay">
        <div class="modal-content large">
            <div class="modal-header">
                <h2><i class="fa-solid fa-video"></i> Virtual Class</h2>
                <span style="cursor:pointer;" onclick="closeMeeting()">&times;</span>
            </div>
            <div class="modal-body" style="padding: 10px;">
                <div id="jitsi-container"></div>
            </div>
        </div>
    </div>

    <div id="assignModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Assignment</h2><span onclick="closeAllModals()" style="cursor:pointer">&times;</span></div>
            <div class="modal-body">
                <div class="input-group"><label>Title</label><input type="text" id="asTitle"></div>
                <div class="input-group"><label>Instructions</label><textarea id="asInstr"></textarea></div>
                <div class="upload-area-small" onclick="document.getElementById('asFile').click()">
                    <input type="file" id="asFile" hidden onchange="showFile(this, 'asFileTxt')">
                    <span id="asFileTxt"><i class="fa-solid fa-paperclip"></i> Attach File</span>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn btn-go" onclick="createAssign()">Assign</button></div>
        </div>
    </div>

    <div id="aiModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Generate Quiz</h2><span onclick="closeAllModals()" style="cursor:pointer">&times;</span></div>
            <div class="modal-body">
                <div class="upload-area-small" onclick="document.getElementById('aiFile').click()">
                    <input type="file" id="aiFile" hidden accept="application/pdf" onchange="showFile(this, 'aiFileTxt')">
                    <span id="aiFileTxt"><i class="fa-solid fa-cloud-arrow-up"></i> Upload PDF</span>
                </div>
                <div class="input-group" style="margin-top:20px;">
                    <label>Instructions for AI</label>
                    <textarea id="aiPrompt" placeholder="e.g. Generate 5 difficult questions about Chapter 3..."></textarea>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn btn-go" id="aiBtn" onclick="generateQuiz()">Generate</button></div>
        </div>
    </div>

    <div id="questModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Question</h2><span onclick="closeAllModals()" style="cursor:pointer">&times;</span></div>
            <div class="modal-body">
                <div class="input-group"><label>Question</label><textarea id="qText"></textarea></div>
            </div>
            <div class="modal-footer"><button class="btn btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn btn-go" onclick="createQuest()">Ask</button></div>
        </div>
    </div>

<script>
    // --- SETUP GLOBALS ---
    let jitsiApi = null;
    console.log("Script initializing...");

    window.switchTab = function(tabName) {
        document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));
        const tab = document.getElementById('tab-' + tabName);
        if(tab) tab.classList.add('active');
        
        document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active-section'));
        const section = document.getElementById(tabName + 'Section');
        if(section) section.classList.add('active-section');
    };

    window.toggleDropdown = function() { 
        const el = document.getElementById('createDropdown'); 
        el.style.display = (el.style.display === 'block') ? 'none' : 'block'; 
    };

    window.onclick = function(e) { 
        if (!e.target.matches('.create-btn') && !e.target.closest('.create-btn')) {
            const dropdown = document.getElementById('createDropdown');
            if(dropdown) dropdown.style.display = 'none'; 
        }
        if (e.target.classList.contains('modal-overlay')) {
            if(e.target.id === 'jitsiModal') window.closeMeeting();
            else window.closeAllModals(); 
        }
    };

    window.openModal = function(id) { 
        const dropdown = document.getElementById('createDropdown');
        if(dropdown) dropdown.style.display = 'none'; 
        document.getElementById(id).style.display = 'flex'; 
    };

    window.closeAllModals = function() { 
        document.querySelectorAll('.modal-overlay').forEach(el => el.style.display = 'none'); 
    };

    window.showFile = function(input, txtId) { 
        if(input.files[0]) { 
            document.getElementById(txtId).innerHTML = `<b>${input.files[0].name}</b>`; 
            document.getElementById(txtId).style.color = '#1a73e8'; 
        } 
    };

    // --- JITSI FUNCTIONS ---
    window.startMeeting = function() {
        document.getElementById('jitsiModal').style.display = 'flex';
        // Use meet.ffmuc.net to avoid 5min limits
        const domain = "meet.ffmuc.net"; 
        const options = {
            roomName: "TechHub_BSIT41_Programming_Room_2026",
            width: "100%", 
            height: 500,
            parentNode: document.querySelector('#jitsi-container'),
            userInfo: { displayName: 'Prof. Jhomari Gandionco' },
            configOverwrite: {
                prejoinPageEnabled: false,
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                disableDeepLinking: true
            }
        };
        document.querySelector('#jitsi-container').innerHTML = "";
        jitsiApi = new JitsiMeetExternalAPI(domain, options);
    };

    window.closeMeeting = function() {
        if (jitsiApi) {
            jitsiApi.executeCommand('hangup');
            jitsiApi.dispose();
            jitsiApi = null;
        }
        document.getElementById('jitsiModal').style.display = 'none';
        document.querySelector('#jitsi-container').innerHTML = "";
    };

    // --- SUPABASE CONFIGURATION ---
    const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0'; 
    
    let supabaseClient;
    let currentUser = { id: "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11" };
    let classworkItems = [];

    // Initialize Supabase
    try {
        if (!window.supabase) {
            throw new Error("Supabase CDN failed to load.");
        }
        supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey);
        console.log("Supabase connected.");
    } catch (err) {
        console.error("Supabase Init Error:", err);
        alert("System Error: Could not connect to database.");
    }

    // --- INITIAL LOAD ---
    document.addEventListener('DOMContentLoaded', () => {
        if(supabaseClient) {
            fetchClasswork();
        } else {
            document.getElementById('loadingState').innerText = "Error: Database not connected.";
        }
    });

    async function fetchClasswork() {
        console.log("Fetching classwork...");
        const loading = document.getElementById('loadingState');
        if(loading) loading.style.display = 'block';
        
        try {
            const { data, error } = await supabaseClient
                .from('classwork')
                .select('*')
                .order('created_at', { ascending: false });

            if (error) throw error;

            classworkItems = data || [];
            renderClassworkList();
            renderStreamFeed();
            
            if(loading) loading.style.display = 'none';

        } catch (err) {
            console.error("Fetch Error:", err);
            if(loading) loading.innerText = "Failed to load classwork. See console.";
        }
    }

    // --- DB ACTIONS ---
    window.createAssign = async function() {
        const title = document.getElementById('asTitle').value;
        const instr = document.getElementById('asInstr').value;
        const fileInput = document.getElementById('asFile');
        const btn = document.querySelector('#assignModal .btn-go');

        if(!title) return alert("Title required");
        btn.innerText = "Saving..."; btn.disabled = true;

        try {
            let fileUrl = null;
            let fileName = null;

            if (fileInput.files[0]) {
                const file = fileInput.files[0];
                fileName = file.name;
                const filePath = `${currentUser.id}/${Date.now()}_${file.name}`;
                const { error: uploadError } = await supabaseClient.storage.from('materials').upload(filePath, file);
                if(uploadError) throw uploadError;
                const { data: publicData } = supabaseClient.storage.from('materials').getPublicUrl(filePath);
                fileUrl = publicData.publicUrl;
            }

            const { error } = await supabaseClient.from('classwork').insert([{
                teacher_id: currentUser.id,
                type: 'assignment',
                title: title,
                description: instr,
                file_name: fileName,
                file_url: fileUrl
            }]);

            if(error) throw error;
            window.closeAllModals();
            fetchClasswork(); 

        } catch(err) {
            alert("Error: " + err.message);
        } finally {
            btn.innerText = "Assign"; btn.disabled = false;
        }
    };

    window.generateQuiz = async function() {
        const file = document.getElementById('aiFile').files[0];
        const prompt = document.getElementById('aiPrompt').value;
        const btn = document.getElementById('aiBtn');

        if (!file) return alert("Please upload a PDF.");
        btn.innerText = "Generating..."; btn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('pdf_file', file);
            formData.append('custom_prompt', prompt);

            const res = await fetch('api/generate_quiz_api.php', { method: 'POST', body: formData });
            const textResponse = await res.text();
            let aiResult = JSON.parse(textResponse);
            if (!aiResult.success) throw new Error(aiResult.message);

            const filePath = `${currentUser.id}/${Date.now()}_${file.name}`;
            await supabaseClient.storage.from('materials').upload(filePath, file);
            const { data: publicData } = supabaseClient.storage.from('materials').getPublicUrl(filePath);

            const { error } = await supabaseClient.from('classwork').insert([{
                teacher_id: currentUser.id,
                type: 'quiz',
                title: 'Quiz: ' + file.name,
                description: 'AI Generated Quiz',
                file_name: file.name,
                file_url: publicData.publicUrl,
                quiz_data: aiResult.questions 
            }]);

            if(error) throw error;
            window.closeAllModals();
            fetchClasswork();

        } catch (err) {
            console.error(err);
            alert("Error: " + err.message);
        } finally {
            btn.innerText = "Generate"; btn.disabled = false;
        }
    };

    window.createQuest = async function() {
        const qText = document.getElementById('qText').value;
        if(!qText) return alert("Question required");
        const btn = document.querySelector('#questModal .btn-go');
        btn.disabled = true;

        try {
            const { error } = await supabaseClient.from('classwork').insert([{
                teacher_id: currentUser.id,
                type: 'question',
                title: qText,
                description: qText
            }]);
            if(error) throw error;
            window.closeAllModals();
            fetchClasswork();
        } catch(err) {
            alert("Error: " + err.message);
        } finally {
            btn.disabled = false;
        }
    };

    // --- RENDER LOGIC ---
    function renderClassworkList() {
        const list = document.getElementById('streamItemsArea');
        if(!list) return;
        list.innerHTML = '';
        
        if(classworkItems.length === 0) {
            list.innerHTML = '<div style="text-align:center; margin-top:50px; color:#999;">No classwork yet. Click Create!</div>';
            return;
        }

        classworkItems.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'stream-item';
            div.onclick = () => window.showDetail(index);
            
            let icon = 'fa-file-text';
            let color = 'icon-assign';
            if(item.type === 'quiz') { icon = 'fa-robot'; color = 'icon-quiz'; }
            if(item.type === 'question') { icon = 'fa-question'; color = 'icon-quest'; }

            div.innerHTML = `
                <div class="item-icon ${color}"><i class="fa-solid ${icon}"></i></div>
                <div class="item-content">
                    <div class="item-title">${item.title}</div>
                    <div class="item-meta">${new Date(item.created_at).toLocaleDateString()} • ${item.file_name ? 'Attachment' : 'No attachment'}</div>
                </div>
            `;
            list.appendChild(div);
        });
    }

    function renderStreamFeed() {
        const feed = document.getElementById('streamFeedArea');
        if(!feed) return;
        feed.innerHTML = '';

        if(classworkItems.length === 0) {
            feed.innerHTML = `<div style="text-align:center; color:#5f6368; padding:20px;">No updates yet.</div>`;
            return;
        }

        // Show simplified version for Stream
        classworkItems.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'stream-item';
            div.style.border = "1px solid #e0e0e0";
            
            // Clicking stream item takes you to Classwork detail
            div.onclick = () => {
                window.switchTab('classwork');
                window.showDetail(index);
            };
            
            let icon = 'fa-clipboard-list';
            let color = 'icon-assign';
            if(item.type === 'quiz') { icon = 'fa-robot'; color = 'icon-quiz'; }

            div.innerHTML = `
                <div class="item-icon ${color}"><i class="fa-solid ${icon}"></i></div>
                <div class="item-content">
                    <div class="item-title">Prof. Gandionco posted a new ${item.type}: ${item.title}</div>
                    <div class="item-meta">${new Date(item.created_at).toDateString()}</div>
                </div>
            `;
            feed.appendChild(div);
        });
    }

    window.showDetail = function(index) {
        const item = classworkItems[index];
        document.getElementById('streamView').style.display = 'none';
        document.querySelector('.create-wrapper').style.display = 'none';
        document.getElementById('detailView').style.display = 'block';

        document.getElementById('detTitle').innerText = item.title;
        document.getElementById('detMeta').innerText = `Type: ${item.type.toUpperCase()}`;

        const body = document.getElementById('detBody');
        body.innerHTML = '';

        if(item.type === 'quiz' && item.quiz_data) {
            item.quiz_data.forEach((q, i) => {
                body.innerHTML += `
                    <div class="quiz-question-card">
                        <div class="q-title">${i+1}. ${q.question}</div>
                        <div class="q-options-grid">
                            ${q.options.map(opt => `<div class="q-opt">${opt}</div>`).join('')}
                        </div>
                        <div class="q-ans"><i class="fa-solid fa-check"></i> Answer: ${q.answer}</div>
                    </div>
                `;
            });
        } else {
            body.innerHTML = `<p style="font-size:1.1rem; line-height:1.6;">${item.description || ''}</p>`;
            if(item.file_url) {
                body.innerHTML += `
                    <div style="margin-top:20px; padding:15px; border:1px solid #ddd; border-radius:6px; display:inline-flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-paperclip" style="color:#1a73e8"></i>
                        <a href="${item.file_url}" target="_blank" style="text-decoration:none; color:#333; font-weight:500;">${item.file_name}</a>
                    </div>
                `;
            }
        }
    };

    window.closeDetail = function() {
        document.getElementById('detailView').style.display = 'none';
        document.getElementById('streamView').style.display = 'block';
        document.querySelector('.create-wrapper').style.display = 'block';
    };
    window.toggleProfileDropdown = function() {
    const profileMenu = document.getElementById('profileDropdown');
    // Close create dropdown if it's open
    const createMenu = document.getElementById('createDropdown');
    if(createMenu) createMenu.style.display = 'none';
    
    profileMenu.style.display = (profileMenu.style.display === 'block') ? 'none' : 'block';
}

// Update your existing window.onclick to handle closing the profile dropdown too
window.onclick = function(e) { 
    // Close Create Dropdown
    if (!e.target.closest('.create-btn')) {
        const createDropdown = document.getElementById('createDropdown');
        if(createDropdown) createDropdown.style.display = 'none'; 
    }
    
    // Close Profile Dropdown
    if (!e.target.closest('.profile-section')) {
        const profileDropdown = document.getElementById('profileDropdown');
        if(profileDropdown) profileDropdown.style.display = 'none';
    }

    // Modal closing logic (keep your existing code)
    if (e.target.classList.contains('modal-overlay')) {
        if(e.target.id === 'jitsiModal') window.closeMeeting();
        else window.closeAllModals(); 
    }
};
</script>
</body>
</html>