<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechHub - Classes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="https://meet.ffmuc.net/external_api.js"></script>

<style>
    /* --- GENERAL RESET --- */
    body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; height: 100vh; display: flex; flex-direction: column; }
    * { box-sizing: border-box; }

    /* --- HEADER --- */
    header { background: white; padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; flex-shrink: 0; z-index: 100; }
    .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; cursor: pointer; }
    .logo-icon { font-size: 32px; color: #1a73e8; }
    .logo-text { font-size: 24px; font-weight: 700; color: #000; }
    .nav-links { display: flex; gap: 30px; height: 100%; }
    .nav-item { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #666; font-weight: 500; height: 100%; cursor: pointer; }
    .nav-item.active { color: #1a73e8; border-bottom: 3px solid #1a73e8; }
    .profile-section { display: flex; align-items: center; gap: 12px; width: 250px; justify-content: flex-end; }
    
    .avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #ddd; background-size: cover; background-position: center; }
    .logout-btn { margin-left: 15px; background: none; border: none; color: #666; font-size: 20px; cursor: pointer; transition: 0.2s; padding: 5px; }
    .logout-btn:hover { color: #e74c3c; transform: scale(1.1); }

    /* --- LAYOUT --- */
    .app-layout { display: flex; flex: 1; overflow: hidden; }
    .sidebar { width: 300px; background: white; display: flex; flex-direction: column; padding: 24px 0; border-right: 1px solid #e0e0e0; }
    .sidebar-item { display: flex; align-items: center; gap: 18px; padding: 12px 30px; color: #5f6368; font-weight: 500; cursor: pointer; border-radius: 0 50px 50px 0; margin-right: 10px; transition: background 0.2s; }
    .sidebar-item:hover { background: #f5f5f5; }
    .sidebar-item.active { background: #e8f0fe; color: #1a73e8; font-weight: 600; }
    .sidebar-item i { width: 24px; text-align: center; font-size: 20px; }

    .main-content { flex: 1; padding: 30px 50px; overflow-y: auto; background: white; position: relative; }
    .section-view { display: none; animation: fadeIn 0.3s; }
    .section-view.active-section { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* --- CLASS GRID --- */
    .action-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .action-header h2 { margin: 0; font-weight: 400; font-size: 24px; color: #333; }
    .create-btn { background: #1a73e8; color: white; border: none; padding: 10px 24px; border-radius: 25px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    
    .class-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .class-card { background: transparent; cursor: pointer; transition: transform 0.2s; position: relative; }
    .class-card:hover { transform: translateY(-3px); }
    .class-image { width: 100%; height: 160px; background-color: #ddd; border-radius: 12px; overflow: hidden; margin-bottom: 10px; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .class-image img { width: 100%; height: 100%; object-fit: cover; }
    .class-text-content { padding: 0 5px; }
    .class-title { font-size: 14px; font-weight: 700; text-transform: uppercase; color: #000; margin-bottom: 2px; }
    .class-subtitle { font-size: 12px; color: #666; font-weight: 500; }
    
    .code-badge { font-size: 11px; background: #e8f0fe; color: #1a73e8; padding: 2px 6px; border-radius: 4px; font-weight: 600; display: inline-block; margin-top: 4px; }

    .card-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 8px; opacity: 0; transition: 0.2s; z-index: 10; }
    .class-card:hover .card-actions { opacity: 1; }
    .action-icon { width: 32px; height: 32px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); font-size: 14px; transition: 0.2s; }
    .action-icon:hover { transform: scale(1.1); }
    .icon-edit { color: #1a73e8; }
    .icon-del { color: #d93025; }

    /* --- INSIDE CLASS VIEW --- */
    .class-banner { height: 240px; border-radius: 8px; background-image: url('https://gstatic.com/classroom/themes/img_read.jpg'); background-size: cover; background-position: center; position: relative; margin-bottom: 25px; }
    .class-banner-content { position: absolute; bottom: 20px; left: 25px; right: 25px; color: white; display: flex; justify-content: space-between; align-items: flex-end; }
    .class-banner-content h1 { margin: 0; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .virtual-btn { background-color: rgba(255, 255, 255, 0.9); color: #1a73e8; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; display: flex; align-items: center; gap: 10px; cursor: pointer; }
    
    .class-code-box {
        position: absolute; top: 20px; right: 20px;
        background: rgba(255, 255, 255, 0.9); padding: 10px 15px;
        border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        display: flex; flex-direction: column; width: 200px;
    }
    .code-label { font-size: 12px; color: #555; font-weight: 600; margin-bottom: 5px; }
    .code-val-row { display: flex; align-items: center; justify-content: space-between; }
    .code-text { font-size: 18px; font-weight: 700; color: #1a73e8; letter-spacing: 1px; }
    .copy-icon { cursor: pointer; color: #666; font-size: 14px; }
    .copy-icon:hover { color: #1a73e8; }

    /* MODALS & INPUTS */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 8px; width: 550px; padding: 0; box-shadow: 0 24px 38px rgba(0,0,0,0.14); }
    .modal-content.grading-modal { width: 700px; max-height: 85vh; overflow: hidden; display: flex; flex-direction: column; }
    
    .modal-header { padding: 15px 24px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 24px; overflow-y: auto; }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; font-weight: 500; margin-bottom: 5px; }
    .input-group input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .modal-footer { padding: 15px 24px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #eee; }
    .btn-cancel { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
    .btn-go { background: #1a73e8; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }

    .dropdown-menu { display: none; position: absolute; top: 45px; right: 0; width: 200px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 8px 0; z-index: 1000; text-align: left; }
    .dropdown-item { padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #333; font-size: 14px; font-weight: 500; }
    .dropdown-item:hover { background: #f5f5f5; }

    .stream-item { background: white; border: 1px solid #dadce0; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; display: flex; align-items: center; gap: 20px; transition: 0.2s; }
    .stream-item:hover { box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
    .item-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0; }
    .icon-assign { background: #e37400; }
    .icon-quiz { background: #1a73e8; }

    /* Grading List Styles */
    .submission-row { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; }
    .sub-meta { font-size: 12px; color: #5f6368; margin-top: 4px; }
    .file-link { display: inline-block; margin-top: 10px; color: #1a73e8; text-decoration: none; border: 1px solid #dadce0; padding: 5px 12px; border-radius: 15px; font-size: 13px; }
    .file-link:hover { background: #f1f3f4; }
    .grade-box { text-align: right; margin-top: 10px; padding-top: 10px; border-top: 1px solid #f0f0f0; }
    .student-comment { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; font-size: 14px; font-style: italic; color: #555; }

    #detailView { display: none; }
    #global-loader { position: fixed; inset:0; background:white; z-index:3000; display:flex; justify-content:center; align-items:center; font-size:18px; color:#666; }

    /* upload area (for AI pdf upload) */
    .upload-area-small {
        border: 2px dashed #c4c4c4;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        background: #fafafa;
        font-size: 14px;
        color: #666;
    }
    .upload-area-small i {
        margin-right: 6px;
    }
</style>
</head>
<body>

<div id="global-loader">Loading...</div>

<header>
    <div class="logo-section" onclick="window.location.href='Dashboard.php'">
        <i class="fa-solid fa-book-open logo-icon"></i><span class="logo-text">TechHub</span>
    </div>
    <nav class="nav-links">
        <a href="Dashboard.php" class="nav-item">Dashboard</a>
        <a href="Classwork.php" id="nav-classes" class="nav-item active">Classes</a>
        <a href="gradebook.php" class="nav-item">Gradebook</a>
    </nav>
    <div class="profile-section">
        <div style="text-align:right;">
            <h4 id="profile-name" style="margin:0; font-size:14px;">Loading...</h4>
            <span style="font-size:12px; color:#777;">Teacher</span>
        </div>
        <div class="avatar" id="profile-avatar"></div>
        <button class="logout-btn" onclick="handleLogout()" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
    </div>
</header>

<div class="app-layout">
    <aside class="sidebar">
        <div id="sidebar-all-classes">
            <div class="sidebar-item active"><i class="fa-solid fa-list"></i> All Classes</div>
        </div>
        <div id="sidebar-single-class" style="display:none;">
            <div class="sidebar-item" onclick="showAllClasses()"><i class="fa-solid fa-arrow-left"></i> Back to All</div>
            <div class="sidebar-item active" id="tab-stream" onclick="switchTab('stream')"><i class="fa-regular fa-comment-dots"></i> Stream</div>
            <div class="sidebar-item" id="tab-classwork" onclick="switchTab('classwork')"><i class="fa-solid fa-clipboard-list"></i> Classwork</div>
            <div class="sidebar-item" id="tab-people" onclick="switchTab('people')"><i class="fa-solid fa-user-group"></i> People</div>
        
        <div class="sidebar-item" id="tab-attendance" onclick="switchTab('attendance')">
    <i class="fa-solid fa-clock-rotate-left"></i> Attendance
</div>
        </div>
    </aside>

    <main class="main-content">
        <div id="view-all-classes" class="section-view active-section">
            <div class="action-header">
                <h2>My Classes</h2>
                <button class="create-btn" onclick="openClassModal()"><i class="fa-solid fa-plus"></i> Create Class</button>
            </div>
            <div id="loadingMsg" style="text-align:center; padding:40px; color:#777;">Loading classes...</div>
            <div class="class-grid" id="classGrid"></div>
        </div>

        <div id="view-single-class" class="section-view">
            <div id="streamSection" class="active-tab-content">
                <div class="class-banner">
                    <div class="class-code-box">
                        <span class="code-label">Class Code</span>
                        <div class="code-val-row">
                            <span class="code-text" id="bannerCode">...</span>
                            <i class="fa-regular fa-copy copy-icon" onclick="copyCode()" title="Copy Code"></i>
                        </div>
                    </div>
                    <div class="class-banner-content">
                        <div>
                            <h1 id="bannerTitle">Loading...</h1>
                            <p id="bannerSubtitle">...</p>
                        </div>
                        <button class="virtual-btn" onclick="startMeeting()">
                            <i class="fa-solid fa-video"></i> Virtual Class
                        </button>
                    </div>
                </div>
                <div id="streamFeedArea" style="margin-top:30px;">Loading stream...</div>
            </div>

            <div id="classworkSection" class="active-tab-content" style="display:none;">
                <div id="streamListView">
                    <div class="action-header">
                        <h2>Classwork</h2>
                        <div style="position: relative;">
                            <button class="create-btn" onclick="toggleDropdown()"><i class="fa-solid fa-plus"></i> Create</button>
                            <div id="createDropdown" class="dropdown-menu">
                                <div class="dropdown-item" onclick="openAssignModal()"><i class="fa-solid fa-file-pen"></i> Assignment</div>
                                <div class="dropdown-item" onclick="openAiModal()"><i class="fa-solid fa-robot"></i> AI Quiz Generator</div>
                            </div>
                        </div>
                    </div>
                    <div id="streamItemsArea"></div>
                </div>
                <div id="detailView">
                    <button style="background:none; border:none; cursor:pointer; color:#1a73e8; margin-bottom:15px; font-size:16px; display:flex; align-items:center; gap:5px;" onclick="closeDetail()">
                        <i class="fa-solid fa-arrow-left"></i> Back to list
                    </button>
                    <div style="background:white; padding:30px; border-radius:8px; border:1px solid #ddd;">
                        <h2 id="detTitle" style="margin-top:0; color:#1a73e8;"></h2>
                        <div id="detBody"></div>
                    </div>
                </div>
            </div>

            <div id="peopleSection" class="active-tab-content" style="display:none; max-width: 800px; margin: 0 auto;">
                <div id="teacherListArea"></div>
                <div id="studentListArea" style="margin-top: 30px;"></div>
            </div>
        </div>
        <div id="attendanceSection" class="active-tab-content" style="display:none;">
    <div class="action-header">
        <h2>Attendance Report</h2>
        <button class="btn-go" onclick="fetchAttendance()"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>
    <table id="attendanceTable" style="width:100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                <th style="padding: 15px;">Student Name</th>
                <th style="padding: 15px;">Time In</th>
                <th style="padding: 15px;">Time Out</th>
                <th style="padding: 15px;">Duration</th>
            </tr>
        </thead>
        <tbody id="attendanceBody">
            </tbody>
    </table>
</div>
    </main>
</div>

<!-- Create Class Modal -->
<div id="createClassModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header"><h2>Create New Class</h2><span style="cursor:pointer;" onclick="closeAllModals()">&times;</span></div>
        <div class="modal-body">
            <div class="input-group"><label>Class Name</label><input type="text" id="newClassName"></div>
            <div class="input-group"><label>Section</label><input type="text" id="newClassSection"></div>
        </div>
        <div class="modal-footer"><button class="btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn-go" onclick="createClass()">Create</button></div>
    </div>
</div>

<!-- New Assignment Modal -->
<div id="assignModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header"><h2>New Assignment</h2><span style="cursor:pointer;" onclick="closeAllModals()">&times;</span></div>
        <div class="modal-body">
            <div class="input-group"><label>Title</label><input type="text" id="asTitle"></div>
            <div class="input-group"><label>Instructions</label><textarea id="asInstr"></textarea></div>
            <div class="input-group"><label>Due Date</label><input type="datetime-local" id="asDueDate"></div>
        </div>
        <div class="modal-footer"><button class="btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn-go" onclick="createAssign()">Assign</button></div>
    </div>
</div>

<!-- AI Quiz Generator Modal (updated with question type) -->
<div id="aiModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header"><h2>Generate Quiz</h2><span style="cursor:pointer" onclick="closeAllModals()">&times;</span></div>
        <div class="modal-body">
            <div class="upload-area-small" onclick="document.getElementById('aiFile').click()">
                <input type="file" id="aiFile" hidden accept="application/pdf" onchange="showFile(this, 'aiFileTxt')">
                <span id="aiFileTxt"><i class="fa-solid fa-cloud-arrow-up"></i> Upload PDF</span>
            </div>

            <div class="input-group" style="margin-top:20px;">
                <label>Question Type</label>
                <select id="aiQuestionType">
                    <option value="mcq">Multiple Choice (4 options)</option>
                    <option value="open">Open-ended (short answer)</option>
                </select>
            </div>

            <div class="input-group" style="margin-top:20px;">
                <label>Instructions for AI</label>
                <textarea id="aiPrompt" placeholder="e.g. Focus on Chapter 3: Binary Trees..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeAllModals()">Cancel</button>
            <button class="btn-go" id="aiBtn" onclick="generateQuiz()">Generate</button>
        </div>
    </div>
</div>

<!-- Jitsi Modal -->
<div id="jitsiModal" class="modal-overlay" style="background: rgba(0,0,0,0.9);">
    <div class="modal-content" style="width: 90%; height: 90%; max-width: none;">
        <div class="modal-header">
            <h2 id="jitsiTitle">Virtual Class</h2>
            <button class="btn-cancel" onclick="closeJitsi()">Close Meeting</button>
        </div>
        <div id="jitsi-container" style="height: calc(100% - 60px); width: 100%;"></div>
    </div>
</div>

<!-- Grading Modal -->
<div id="gradingModal" class="modal-overlay">
    <div class="modal-content grading-modal">
        <div class="modal-header">
            <h2 id="gradingTitle">Submissions</h2>
            <span style="cursor:pointer" onclick="closeGradingModal()">&times;</span>
        </div>
        <div class="modal-body" id="gradingList">
            <p style="text-align:center; color:#777;">Loading...</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeGradingModal()">Close</button>
        </div>
    </div>
</div>

<!-- Quiz Editor Modal (NEW) -->
<div id="quizEditorModal" class="modal-overlay">
    <div class="modal-content grading-modal">
        <div class="modal-header">
            <h2 id="quizEditorTitle">Edit Generated Quiz</h2>
            <span style="cursor:pointer" onclick="closeQuizEditor()">&times;</span>
        </div>
        <div class="modal-body" id="quizEditorList" style="max-height:60vh; overflow-y:auto;">
            <!-- dynamic -->
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeQuizEditor()">Cancel</button>
            <button class="btn-go" onclick="saveEditedQuiz()">Save Quiz</button>
        </div>
    </div>
</div>

<script>
    const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
    let supabaseClient;
    
    let currentUser = null; 
    let currentClassId = null;
    let userFullName = "Teacher"; 
    let jitsiApi = null;

    // NEW: store latest generated quiz for editing
    let latestGeneratedQuestions = [];
    let latestQuizFileName = '';

    try { supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey); } 
    catch (e) { console.error(e); }

    document.addEventListener('DOMContentLoaded', async () => {
        const { data: { session } } = await supabaseClient.auth.getSession();
        if (!session) { window.location.href = 'teacher_login.php'; return; }
        currentUser = session.user;
        await fetchUserProfile();
        document.getElementById('global-loader').style.display = 'none';

        const urlParams = new URLSearchParams(window.location.search);
        const cid = urlParams.get('class_id');
        if (cid) openClass(cid); 
        else fetchClasses(); 
    });

    async function fetchUserProfile() {
        try {
            const { data: profile } = await supabaseClient.from('profiles').select('full_name, role').eq('id', currentUser.id).single();
            if (profile) {
                userFullName = profile.full_name;
                document.getElementById('profile-name').innerText = profile.full_name;
                const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(profile.full_name)}&background=0D8ABC&color=fff`;
                document.getElementById('profile-avatar').style.backgroundImage = `url('${avatarUrl}')`;
            }
        } catch (err) { console.error("Profile Error:", err); }
    }

    async function openClass(classId) {
        currentClassId = classId;
        const url = new URL(window.location);
        url.searchParams.set('class_id', classId);
        window.history.pushState({}, '', url);

        document.getElementById('view-all-classes').style.display = 'none';
        document.getElementById('view-single-class').style.display = 'block';
        document.getElementById('sidebar-all-classes').style.display = 'none';
        document.getElementById('sidebar-single-class').style.display = 'block';

        const { data: cls } = await supabaseClient.from('classes').select('*').eq('id', classId).single();
        if(cls) {
            document.getElementById('bannerTitle').innerText = cls.title;
            document.getElementById('bannerSubtitle').innerText = cls.section;
            document.getElementById('bannerCode').innerText = cls.class_code || '---';
        }
        fetchClasswork();
    }

    function showAllClasses() {
        document.getElementById('view-all-classes').style.display = 'block';
        document.getElementById('view-single-class').style.display = 'none';
        document.getElementById('sidebar-all-classes').style.display = 'block';
        document.getElementById('sidebar-single-class').style.display = 'none';
        const url = new URL(window.location);
        url.searchParams.delete('class_id');
        window.history.pushState({}, '', url);
        fetchClasses();
    }

    async function fetchClasses() {
        const grid = document.getElementById('classGrid');
        const { data } = await supabaseClient.from('classes').select('*').eq('teacher_id', currentUser.id).order('created_at', { ascending: false });
        document.getElementById('loadingMsg').style.display = 'none';
        grid.innerHTML = '';
        if(!data || data.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#999;">No classes found.</div>';
            return;
        }
        data.forEach((cls) => {
            const card = document.createElement('div');
            card.className = 'class-card';
            const gradient = pickGradient(cls.id || cls.title);
            card.innerHTML = `<div onclick="openClass('${cls.id}')"><div class="class-image" style="background: ${gradient};"></div><div class="class-text-content"><div class="class-title">${cls.title}</div><div class="class-subtitle">${cls.section}</div></div></div>`;
            grid.appendChild(card);
        });
    }

    async function fetchClasswork() {
        const { data } = await supabaseClient.from('classwork').select('*').eq('class_id', currentClassId).order('created_at', { ascending: false });
        const list = document.getElementById('streamFeedArea');
        const items = document.getElementById('streamItemsArea');
        list.innerHTML = ''; items.innerHTML = '';
        if(!data || data.length === 0) {
            list.innerHTML = '<div style="text-align:center; color:#999;">No content.</div>';
            return;
        }
        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'stream-item';
            div.onclick = () => showDetail(item);
            let icon = item.type === 'quiz' ? 'fa-robot' : 'fa-file-lines';
            div.innerHTML = `<div class="item-icon ${item.type === 'quiz' ? 'icon-quiz' : 'icon-assign'}"><i class="fa-solid ${icon}"></i></div><div class="item-content"><div class="item-title">${item.title}</div><div class="item-meta">${new Date(item.created_at).toLocaleDateString()}</div></div>`;
            items.appendChild(div); 
            list.appendChild(div.cloneNode(true));
        });
    }

    // --- Detail View Logic ---
    function showDetail(item) {
        document.getElementById('streamListView').style.display = 'none';
        document.getElementById('detailView').style.display = 'block';
        document.getElementById('detTitle').innerText = item.title;
        
        const safeTitle = item.title.replace(/'/g, "\\'");
        let content = `<div style="display:flex; justify-content:space-between; align-items:start;">`;
        content += `<div><p style="font-size:1.1rem; color:#555;">${item.description || ''}</p>`;
        if (item.due_date) content += `<p style="color:#e37400; font-weight:500;"><i class="fa-regular fa-clock"></i> Due: ${new Date(item.due_date).toLocaleString()}</p>`;
        content += `</div><button onclick="openGradingModal('${item.id}', '${safeTitle}')" style="background:#00C060; color:white; border:none; padding:10px 20px; border-radius:25px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-list-check"></i> View Submissions</button></div>`;

        if(item.type === 'quiz' && item.quiz_data) {
            let questions = item.quiz_data;
            if (typeof questions === 'string') { try { questions = JSON.parse(questions); } catch(e) {} }
            if (Array.isArray(questions)) {
                content += `<div style="margin-top:20px;">`;
                questions.forEach((q, i) => {
                    const isMcq = (q.type || 'mcq') === 'mcq';
                    content += `
                        <div style="background:white; padding:20px; border:1px solid #eee; border-radius:8px; margin-bottom:15px;">
                            <div style="font-weight:600;">${i+1}. ${q.question}</div>
                    `;
                    if (isMcq && Array.isArray(q.options) && q.options.length) {
                        content += `<ul style="margin-top:8px; padding-left:20px;">`;
                        q.options.forEach(opt => {
                            const bold = (opt === q.answer) ? 'font-weight:600; color:#137333;' : '';
                            content += `<li style="${bold}">${opt}</li>`;
                        });
                        content += `</ul>`;
                    }
                    content += `<div style="margin-top:10px; color:#137333;">Answer: ${q.answer || '(no answer set)'}</div></div>`;
                });
                content += `</div>`;
            }
        }
        document.getElementById('detBody').innerHTML = content;
    }

    function closeDetail() {
        document.getElementById('detailView').style.display = 'none';
        document.getElementById('streamListView').style.display = 'block';
    }

    // --- SEPARATED GRADING FUNCTIONS ---
    async function fetchSubmissionsForClasswork(classworkId) {
        const { data: submissions, error } = await supabaseClient
            .from('submissions')
            .select(`*, student:profiles ( full_name, email )`)
            .eq('classwork_id', classworkId)
            .order('created_at', { ascending: false });

        if (error) {
            console.error("Error:", error);
            return { toGrade: [], graded: [] };
        }

        const graded = submissions.filter(sub => sub.status === 'graded');
        const toGrade = submissions.filter(sub => sub.status !== 'graded');
        return { toGrade, graded };
    }

    async function openGradingModal(classworkId, title) {
        document.getElementById('gradingModal').style.display = 'flex';
        document.getElementById('gradingTitle').innerText = "Submissions: " + title;
        document.getElementById('gradingList').innerHTML = '<p style="text-align:center;">Loading submissions...</p>';

        const { toGrade, graded } = await fetchSubmissionsForClasswork(classworkId);

        renderSeparatedSubmissions(toGrade, graded);
    }

    function renderSeparatedSubmissions(toGrade, graded) {
        const container = document.getElementById('gradingList');
        container.innerHTML = '';

        // SECTION A: Needs Grading
        if (toGrade.length > 0) {
            container.innerHTML += `<h3 style="color:#e37400; border-bottom:1px solid #ddd; padding-bottom:5px; margin-top:0;">Needs Grading (${toGrade.length})</h3>`;
            toGrade.forEach(sub => container.innerHTML += createSubmissionCard(sub));
        } else {
            container.innerHTML += `<div style="padding:20px; text-align:center; color:#888; background:#f9f9f9; border-radius:8px; margin-bottom:20px;">No pending work to grade!</div>`;
        }

        // SECTION B: Already Graded
        if (graded.length > 0) {
            container.innerHTML += `<h3 style="color:#137333; border-bottom:1px solid #ddd; padding-bottom:5px; margin-top:30px;">Graded (${graded.length})</h3>`;
            graded.forEach(sub => container.innerHTML += createSubmissionCard(sub));
        }
    }

 function createSubmissionCard(sub) {
    const name = sub.student ? sub.student.full_name : "Unknown Student";
    const email = sub.student ? sub.student.email : "";
    const date = new Date(sub.created_at).toLocaleString();
    const grade = sub.grade || "";

    let detailsHtml = "";

    if (sub.content) {
        let parsed = null;
        try {
            parsed = JSON.parse(sub.content);
        } catch (e) {
            parsed = null;
        }

        // If content is JSON from a quiz
        if (parsed && Array.isArray(parsed.questions)) {
            const summary = parsed.summary || {};
            const comment = parsed.comment || "";

            detailsHtml += `<div class="student-comment">`;

            if (summary && summary.totalMcq != null) {
                const total = summary.totalMcq;
                const correct = summary.correctMcq;
                const g = summary.grade;
                detailsHtml += `<div><b>Score:</b> ${correct}/${total} (${g ?? 0}%)</div>`;
            } else if (summary && summary.grade != null) {
                detailsHtml += `<div><b>Grade:</b> ${summary.grade}</div>`;
            }

            parsed.questions.forEach((q, idx) => {
                const qText = escapeHtml(q.question || "");
                const stud = escapeHtml(q.studentAnswer || "");
                const corr = escapeHtml(q.correctAnswer || "");

                detailsHtml += `
                    <div style="margin-top:8px;">
                        <div><b>Q${idx+1}.</b> ${qText}</div>
                        <div><b>Your answer:</b> ${stud || '<i>no answer</i>'}</div>
                        ${corr ? `<div><b>Correct:</b> ${corr}</div>` : ''}
                    </div>`;
            });

            if (comment) {
                detailsHtml += `<div style="margin-top:8px;"><b>Student comment:</b> ${escapeHtml(comment)}</div>`;
            }

            detailsHtml += `</div>`;
        } else {
            // Old / non-quiz submissions – just show the text
            detailsHtml = `<div class="student-comment">${escapeHtml(sub.content).replace(/\n/g, '<br>')}</div>`;
        }
    }

    return `
    <div class="submission-row" style="border-left: 4px solid ${sub.status === 'graded' ? '#137333' : '#e37400'};">
        <div style="display:flex; justify-content:space-between;">
            <div>
                <div style="font-weight:600; color:#3c4043;">${name}</div>
                <div class="sub-meta">${email} • ${date}</div>
            </div>
            <div style="font-weight:bold; font-size:12px; color:${sub.status === 'graded' ? '#137333' : '#e37400'};">
                ${sub.status === 'graded' ? 'DONE' : 'NEEDS REVIEW'}
            </div>
        </div>

        ${detailsHtml}
        
        ${ sub.file_url ? `<a href="${sub.file_url}" target="_blank" class="file-link"><i class="fa-solid fa-paperclip"></i> View Attached Work</a>` : '' }

        <div class="grade-box">
            <input type="number" placeholder="/100" value="${grade}" id="grade-${sub.id}" style="width:70px; padding:5px; border:1px solid #ccc; border-radius:4px;">
            <button onclick="saveGrade(${sub.id})" style="background:#1a73e8; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:500;">
                ${sub.status === 'graded' ? 'Update Grade' : 'Return Grade'}
            </button>
        </div>
    </div>`;
}


    async function saveGrade(subId) {
        const val = document.getElementById('grade-'+subId).value;
        const btn = document.getElementById('grade-'+subId).nextElementSibling;
        
        if (val === "" || val < 0 || val > 100) {
            alert("Please enter a valid grade (0-100)");
            return;
        }

        btn.innerText = "Saving...";
        btn.disabled = true;

        const { error } = await supabaseClient
            .from('submissions')
            .update({ grade: val, status: 'graded' })
            .eq('id', subId);

        if(error) {
            console.error(error);
            alert("Error saving grade: " + error.message);
            btn.innerText = "Return Grade";
            btn.disabled = false;
        } else {
            btn.innerText = "Saved!";
            btn.style.backgroundColor = "#137333"; 
            setTimeout(() => {
                btn.innerText = "Return Grade";
                btn.style.backgroundColor = "#1a73e8"; 
                btn.disabled = false;
            }, 1000);
        }
    }

    function closeGradingModal() { document.getElementById('gradingModal').style.display = 'none'; }

    // Open the Create Class modal
    function openClassModal() {
        const nameEl = document.getElementById('newClassName');
        const sectionEl = document.getElementById('newClassSection');
        if (nameEl) { nameEl.value = ''; nameEl.focus(); }
        if (sectionEl) sectionEl.value = '';

        const modal = document.getElementById('createClassModal');
        if (modal) modal.style.display = 'flex';
    }
    
    async function createClass() {
        const title = document.getElementById('newClassName').value;
        const section = document.getElementById('newClassSection').value;
        
        if (!title) return alert("Class Name is required");
        
        const btn = document.querySelector('#createClassModal .btn-go');
        const oldText = btn.innerText;
        btn.innerText = "Creating...";
        btn.disabled = true;

        try {
            const code = Math.random().toString(36).substring(2, 9).toUpperCase();

            const { data, error } = await supabaseClient
                .from('classes')
                .insert([{ 
                    teacher_id: currentUser.id, 
                    title: title, 
                    section: section, 
                    class_code: code 
                }])
                .select();

            if (error) {
                console.error("Supabase Error:", error);
                alert("Failed to create class: " + error.message);
            } else {
                closeAllModals();
                fetchClasses();
            }
        } catch (err) {
            console.error("Unexpected Error:", err);
            alert("Unexpected error: " + err.message);
        } finally {
            btn.innerText = oldText;
            btn.disabled = false;
        }
    }

    async function createAssign() {
        const title = document.getElementById('asTitle').value;
        const desc = document.getElementById('asInstr').value;
        const due = document.getElementById('asDueDate').value;
        if(!title) return alert("Required");
        await supabaseClient.from('classwork').insert([{ 
            teacher_id: currentUser.id, 
            class_id: currentClassId, 
            type: 'assignment', 
            title, 
            description: desc, 
            due_date: due ? new Date(due).toISOString() : null 
        }]);
        closeAllModals();
        fetchClasswork();
    }

    // UPDATED: Generate Quiz using AI and open editor
    async function generateQuiz() {
        const file = document.getElementById('aiFile').files[0];
        const prompt = document.getElementById('aiPrompt').value;
        const qType = document.getElementById('aiQuestionType').value || 'mcq';
        const btn = document.getElementById('aiBtn');

        if (!file) {
            return alert("Upload PDF");
        }

        btn.innerText = "Generating...";
        btn.disabled = true;

        try {
            const fd = new FormData();
            fd.append('pdf_file', file);
            fd.append('custom_prompt', prompt);
            fd.append('question_type', qType);

            const res = await fetch('api/generate_quiz_api.php', { method:'POST', body:fd });
            const result = await res.json();
            
            if(result.success) {
                latestGeneratedQuestions = Array.isArray(result.questions) ? result.questions : [];
                latestQuizFileName = file.name;

                if (latestGeneratedQuestions.length === 0) {
                    alert("AI did not return any questions.");
                } else {
                    closeAllModals();
                    openQuizEditor();
                }
            } else {
                alert(result.message || "Error from AI");
            }
        } catch(e) {
            console.error(e);
            alert("Error generating quiz");
        }
        finally {
            btn.innerText = "Generate";
            btn.disabled = false;
        }
    }

    // --- QUIZ EDITOR FUNCTIONS (NEW) ---
    function openQuizEditor() {
        const modal = document.getElementById('quizEditorModal');
        const list = document.getElementById('quizEditorList');
        const qTypeDefault = document.getElementById('aiQuestionType').value || 'mcq';

        document.getElementById('quizEditorTitle').innerText = "Edit Generated Quiz";

        list.innerHTML = '';

        latestGeneratedQuestions.forEach((q, idx) => {
            const type = (q.type || qTypeDefault || 'mcq').toLowerCase();
            const options = Array.isArray(q.options) ? q.options : [];

            const safeQuestion = q.question || '';
            const safeAnswer = q.answer || '';

            let optionsHtml = '';
            for (let i = 0; i < 4; i++) {
                const optVal = options[i] || '';
                const isCorrect = (optVal && optVal === safeAnswer);
                optionsHtml += `
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:5px;">
                        <input type="radio" name="q${idx}-correct" value="${i}" ${isCorrect ? 'checked' : ''}>
                        <input type="text" class="opt-input" data-q="${idx}" data-opt="${i}" value="${optVal}" placeholder="Option ${String.fromCharCode(65+i)}" style="flex:1; padding:6px 8px;">
                    </div>
                `;
            }

            const isMcq = type === 'mcq';

            const block = document.createElement('div');
            block.className = 'quiz-question-edit';
            block.setAttribute('data-index', idx);

            block.style.border = '1px solid #e0e0e0';
            block.style.borderRadius = '8px';
            block.style.padding = '12px 15px';
            block.style.marginBottom = '12px';
            block.style.background = '#fff';

            block.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <div style="font-weight:600; color:#3c4043;">Question ${idx + 1}</div>
                    <select class="qe-type" data-index="${idx}">
                        <option value="mcq" ${isMcq ? 'selected' : ''}>Multiple Choice</option>
                        <option value="open" ${!isMcq ? 'selected' : ''}>Open-ended</option>
                    </select>
                </div>
                <textarea class="qe-question" placeholder="Question text..." style="width:100%; min-height:60px; padding:6px 8px; margin-bottom:8px;">${safeQuestion}</textarea>

                <div class="qe-mcq-section" style="${isMcq ? '' : 'display:none;'}; margin-bottom:8px;">
                    <div style="font-size:13px; font-weight:600; margin-bottom:5px;">Options (select correct answer):</div>
                    ${optionsHtml}
                </div>

                <div class="qe-open-section" style="${!isMcq ? '' : 'display:none;'}; margin-bottom:8px;">
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:4px;">Model Answer / Key Points</label>
                    <textarea class="qe-answer-open" style="width:100%; min-height:50px; padding:6px 8px;">${isMcq ? '' : safeAnswer}</textarea>
                </div>
            `;

            list.appendChild(block);
        });

        // delegate: toggle MCQ/Open sections
        list.addEventListener('change', function(e) {
            if (e.target.classList.contains('qe-type')) {
                const idx = e.target.getAttribute('data-index');
                const parent = list.querySelector(`.quiz-question-edit[data-index="${idx}"]`);
                if (!parent) return;
                const mcqSection = parent.querySelector('.qe-mcq-section');
                const openSection = parent.querySelector('.qe-open-section');

                if (e.target.value === 'mcq') {
                    mcqSection.style.display = '';
                    openSection.style.display = 'none';
                } else {
                    mcqSection.style.display = 'none';
                    openSection.style.display = '';
                }
            }
        });

        modal.style.display = 'flex';
    }

    function closeQuizEditor() {
        document.getElementById('quizEditorModal').style.display = 'none';
    }

    async function saveEditedQuiz() {
        const container = document.getElementById('quizEditorList');
        const blocks = container.querySelectorAll('.quiz-question-edit');

        if (blocks.length === 0) {
            alert("No questions to save.");
            return;
        }

        const finalQuestions = [];

        blocks.forEach(block => {
            const idx = block.getAttribute('data-index');
            const typeSel = block.querySelector('.qe-type');
            const qTextEl = block.querySelector('.qe-question');
            const isMcq = typeSel.value === 'mcq';

            const questionText = qTextEl.value.trim();
            if (!questionText) return;

            let options = [];
            let answer = '';

            if (isMcq) {
                const optInputs = block.querySelectorAll('.opt-input');
                optInputs.forEach((inp, i) => {
                    options[i] = inp.value.trim();
                });

                const checkedRadio = block.querySelector(`input[type="radio"][name="q${idx}-correct"]:checked`);
                if (checkedRadio) {
                    const correctIndex = parseInt(checkedRadio.value, 10);
                    if (!isNaN(correctIndex) && options[correctIndex]) {
                        answer = options[correctIndex];
                    }
                }
            } else {
                const ansOpen = block.querySelector('.qe-answer-open');
                answer = ansOpen ? ansOpen.value.trim() : '';
                options = [];
            }

            finalQuestions.push({
                type: isMcq ? 'mcq' : 'open',
                question: questionText,
                options: options,
                answer: answer
            });
        });

        if (finalQuestions.length === 0) {
            alert("All questions are empty. Nothing to save.");
            return;
        }

        try {
            const title = 'Quiz: ' + (latestQuizFileName || 'AI Generated');
            const desc = 'AI Generated & Edited';

            const { error } = await supabaseClient
                .from('classwork')
                .insert([{
                    teacher_id: currentUser.id,
                    class_id: currentClassId,
                    type: 'quiz',
                    title: title,
                    description: desc,
                    file_name: latestQuizFileName || null,
                    quiz_data: finalQuestions
                }]);

            if (error) {
                console.error(error);
                alert("Error saving quiz: " + error.message);
            } else {
                closeQuizEditor();
                fetchClasswork();
            }
        } catch (e) {
            console.error(e);
            alert("Unexpected error saving quiz");
        }
    }

    // --- HELPERS ---
    function showFile(input, txtId) { 
        if(input.files[0]) { 
            document.getElementById(txtId).innerHTML = `<i class="fa-solid fa-file-pdf"></i> <b>${input.files[0].name}</b>`; 
            document.getElementById(txtId).style.color = '#333'; 
        } 
    }
    
    // --- Virtual Class Sync ---
    async function startMeeting() {
        await supabaseClient.from('classes').update({ meeting_active: true }).eq('id', currentClassId);
        const roomName = "TechHub_Room_" + currentClassId;
        document.getElementById('jitsiTitle').innerText = "Virtual Class: " + document.getElementById('bannerTitle').innerText;
        document.getElementById('jitsiModal').style.display = 'flex';

        const options = {
            roomName: roomName,
            width: "100%",
            height: "100%",
            parentNode: document.querySelector('#jitsi-container'),
            userInfo: { displayName: userFullName },
            configOverwrite: { startWithAudioMuted: true, prejoinPageEnabled: false }
        };

        if (jitsiApi) jitsiApi.dispose();
        jitsiApi = new JitsiMeetExternalAPI("meet.ffmuc.net", options);

        jitsiApi.addEventListener('videoConferenceLeft', () => closeJitsi());
    }

    async function closeJitsi() {
    // 1. End meeting status
    await supabaseClient.from('classes').update({ meeting_active: false }).eq('id', currentClassId);

    // 2. Log Teacher Time Out
    if (window.currentAttendanceId) {
        const timeOut = new Date();
        await supabaseClient
            .from('attendance')
            .update({ time_out: timeOut.toISOString() })
            .eq('id', window.currentAttendanceId);
        window.currentAttendanceId = null;
    }

    if (jitsiApi) { jitsiApi.dispose(); jitsiApi = null; }
    document.getElementById('jitsiModal').style.display = 'none';
}
    function closeAllModals() { document.querySelectorAll('.modal-overlay').forEach(e => e.style.display = 'none'); }
    function toggleDropdown() { document.getElementById('createDropdown').style.display = (document.getElementById('createDropdown').style.display === 'block') ? 'none' : 'block'; }
    function copyCode() { navigator.clipboard.writeText(document.getElementById('bannerCode').innerText); alert("Copied!"); }
    async function handleLogout() { if(confirm("Log out?")) { await supabaseClient.auth.signOut(); window.location.href = 'teacher_login.php'; } }
    
    function openAssignModal() {
        closeAllModals();
        const modal = document.getElementById('assignModal');
        if (!modal) return;
        const t = document.getElementById('asTitle'); if (t) t.value = '';
        const instr = document.getElementById('asInstr'); if (instr) instr.value = '';
        const due = document.getElementById('asDueDate'); if (due) due.value = '';
        const dd = document.getElementById('createDropdown'); if (dd) dd.style.display = 'none';
        modal.style.display = 'flex';
        if (t) t.focus();
    }

    function openAiModal() {
        closeAllModals();
        const modal = document.getElementById('aiModal');
        if (!modal) return;
        const file = document.getElementById('aiFile'); if (file) file.value = '';
        const fileTxt = document.getElementById('aiFileTxt'); if (fileTxt) fileTxt.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> Upload PDF`;
        const prompt = document.getElementById('aiPrompt'); if (prompt) prompt.value = '';
        const dd = document.getElementById('createDropdown'); if (dd) dd.style.display = 'none';
        modal.style.display = 'flex';
        if (prompt) prompt.focus();
    }

    function switchTab(tab) {
        document.querySelectorAll('.sidebar-item').forEach(e => e.classList.remove('active'));
        document.getElementById('tab-'+tab).classList.add('active');
        document.querySelectorAll('.active-tab-content').forEach(e => e.style.display = 'none');
        if(tab === 'stream') document.getElementById('streamSection').style.display = 'block';
        if(tab === 'classwork') document.getElementById('classworkSection').style.display = 'block';
        if(tab === 'people') {
            document.getElementById('peopleSection').style.display = 'block';
            fetchPeople();
        }
    }

    async function fetchPeople() {
        const teacherArea = document.getElementById('teacherListArea');
        const studentArea = document.getElementById('studentListArea');
        const { data: classData } = await supabaseClient.from('classes').select(`teacher_id, teacher:profiles!teacher_id (full_name)`).eq('id', currentClassId).single();

        if (classData) {
            const tName = Array.isArray(classData.teacher) ? classData.teacher[0]?.full_name : classData.teacher?.full_name;
            teacherArea.innerHTML = `<h2 style="color:#1a73e8; font-size: 30px; font-weight:400; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px;">Teachers</h2><div style="display:flex; align-items:center; gap:15px; padding: 10px 0;"><div style="width:40px; height:40px; background:#e0e0e0; border-radius:50%; display:flex; justify-content:center; align-items:center;"><i class="fa-solid fa-user"></i></div><span style="font-weight:500; font-size: 16px;">${tName || 'Unknown'}</span></div>`;
        }

        const { data: students } = await supabaseClient.from('enrollments').select(`student:profiles (full_name)`).eq('class_id', currentClassId);
        let sHtml = `<div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px;"><h2 style="color:#1a73e8; font-size: 30px; font-weight:400; margin:0;">Students</h2><span style="color:#1a73e8; font-weight:500;">${students ? students.length : 0} students</span></div>`;

        if (students && students.length > 0) {
            students.forEach(item => {
                const name = (Array.isArray(item.student) ? item.student[0]?.full_name : item.student?.full_name) || "Student";
                sHtml += `<div style="display:flex; align-items:center; gap:15px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;"><div style="width:35px; height:35px; background:#1967d2; color:white; border-radius:50%; display:flex; justify-content:center; align-items:center; font-size:14px;">${name.charAt(0).toUpperCase()}</div><span style="font-weight:500; color:#3c4043;">${name}</span></div>`;
            });
        } else { sHtml += `<div style="color:#777; font-style:italic;">No students enrolled yet.</div>`; }
        studentArea.innerHTML = sHtml;
    }

    // gradient generator
    function pickGradient(seed) {
        if (!seed) seed = Math.random().toString();
        let hash = 0;
        for (let i = 0; i < seed.length; i++) {
            hash = ((hash << 5) - hash) + seed.charCodeAt(i);
            hash |= 0;
        }
        hash = Math.abs(hash);
        const h1 = hash % 360;
        const h2 = (h1 + 40 + (hash % 80)) % 360;
        return `linear-gradient(135deg, hsl(${h1},70%,60%), hsl(${h2},70%,48%))`;
    }

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}
async function fetchAttendance() {
    const tbody = document.getElementById('attendanceBody');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px;">Loading attendance...</td></tr>';

    // Fetch from the View we created in Step 1
    const { data, error } = await supabaseClient
        .from('class_attendance_summary')
        .select('*')
        .eq('class_id', currentClassId)
        .order('time_in', { ascending: false });

    if (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="4">Error loading data.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px;">No attendance records found.</td></tr>';
        return;
    }

    data.forEach(record => {
        const timeIn = new Date(record.time_in).toLocaleString();
        const timeOut = record.time_out ? new Date(record.time_out).toLocaleString() : '<span style="color:green;">Still in Meeting</span>';
        const duration = record.duration_minutes ? `${record.duration_minutes} mins` : '--';

        const row = `
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px;">${record.student_name}</td>
                <td style="padding: 15px;">${timeIn}</td>
                <td style="padding: 15px;">${timeOut}</td>
                <td style="padding: 15px; font-weight: bold;">${duration}</td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.active-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));

    // Show selected
    document.getElementById(tabName + 'Section').style.display = 'block';
    document.getElementById('tab-' + tabName).classList.add('active');

    if (tabName === 'attendance') {
        fetchAttendance();
    }
}
</script>
</body>
</html>
