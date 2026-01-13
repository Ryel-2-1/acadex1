<?php
session_start();
// Security Check
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
//    header("Location: teacher_login.php");
//    exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechHub - Classes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="https://meet.jit.si/external_api.js"></script>

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
    .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff'); background-size: cover; }
     .profile-info { text-align: right; }
        .profile-info h4 { margin: 0; font-size: 14px; color: #333; }
        .profile-info span { font-size: 12px; color: #777; }
                /* --- Logout Button Style --- */
.logout-btn {
    margin-left: 15px;
    background: none;
    border: none;
    color: #666;
    font-size: 20px;
    cursor: pointer;
    transition: color 0.2s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 5px;
}

.logout-btn:hover {
    color: #e74c3c; /* Red color on hover */
    transform: scale(1.1);
}
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

    /* --- NEW: EDIT/DELETE BUTTONS CSS --- */
    .card-actions { 
        position: absolute; top: 10px; right: 10px; 
        display: flex; gap: 8px; opacity: 0; transition: 0.2s; z-index: 10; 
    }
    .class-card:hover .card-actions { opacity: 1; }
    
    .action-icon { 
        width: 32px; height: 32px; background: white; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
        font-size: 14px; transition: 0.2s;
    }
    .action-icon:hover { transform: scale(1.1); }
    .icon-edit { color: #1a73e8; }
    .icon-edit:hover { background: #e8f0fe; }
    .icon-del { color: #d93025; }
    .icon-del:hover { background: #fce8e6; }

    /* --- INSIDE CLASS VIEW --- */
    .class-banner { height: 240px; border-radius: 8px; background-image: url('https://gstatic.com/classroom/themes/img_read.jpg'); background-size: cover; background-position: center; position: relative; margin-bottom: 25px; }
    .class-banner-content { position: absolute; bottom: 20px; left: 25px; right: 25px; color: white; display: flex; justify-content: space-between; align-items: flex-end; }
    .class-banner-content h1 { margin: 0; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .virtual-btn { background-color: rgba(255, 255, 255, 0.9); color: #1a73e8; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; display: flex; align-items: center; gap: 10px; cursor: pointer; }
    
    /* MODALS */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 8px; width: 550px; padding: 0; box-shadow: 0 24px 38px rgba(0,0,0,0.14); }
    .modal-header { padding: 15px 24px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 24px; }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; font-weight: 500; margin-bottom: 5px; }
    .input-group input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .modal-footer { padding: 15px 24px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #eee; }
    .btn-cancel { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
    .btn-go { background: #1a73e8; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }

    /* Upload Area Style */
    .upload-area-small { border: 2px dashed #dadce0; padding: 20px; text-align: center; background: #f8f9fa; cursor: pointer; color: #1a73e8; font-weight: 500; border-radius: 6px; transition: 0.2s; }
    .upload-area-small:hover { background: #eef6fc; border-color: #1a73e8; }

    /* DROP DOWN */
    .dropdown-menu { display: none; position: absolute; top: 45px; right: 0; width: 200px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 8px 0; z-index: 1000; text-align: left; }
    .dropdown-item { padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #333; font-size: 14px; font-weight: 500; }
    .dropdown-item:hover { background: #f5f5f5; }

    /* Stream Items */
    .stream-item { background: white; border: 1px solid #dadce0; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; display: flex; align-items: center; gap: 20px; transition: 0.2s; }
    .stream-item:hover { box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
    .item-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0; }
    .icon-assign { background: #e37400; }
    .icon-quiz { background: #1a73e8; }

    #detailView { display: none; }
</style>
</head>
<body>

  <header>
        <div class="logo-section" onclick="window.location.href='Dashboard.php'">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
      <nav class="nav-links">
    <a href="Dashboard.php" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
    <a href="Classwork.php" id="nav-classes" class="nav-item active"><i class="fa-solid fa-book"></i> Classes</a>
    <a href="gradebook.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Gradebook</a>
</nav>
        <div class="profile-section">
    <div style="text-align:right;">
        <h4 style="margin:0; font-size:14px;">Prof. Jhomari</h4>
        <span style="font-size:12px; color:#777;">Teacher</span>
    </div>
    <div class="avatar"></div>
    <button class="logout-btn" onclick="handleLogout()" title="Logout">
        <i class="fa-solid fa-right-from-bracket"></i>
    </button>
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
            </div>
        </aside>

        <main class="main-content">
            
            <div id="view-all-classes" class="section-view active-section">
                <div class="action-header">
                    <h2>My Classes</h2>
                    <button class="create-btn" onclick="openClassModal()">
                        <i class="fa-solid fa-plus"></i> Create Class
                    </button>
                </div>
                <div id="loadingMsg" style="text-align:center; padding:40px; color:#777;">Loading classes...</div>
                <div class="class-grid" id="classGrid"></div>
            </div>

            <div id="view-single-class" class="section-view">
                
                <div id="streamSection" class="active-tab-content">
                    <div class="class-banner">
                        <div class="class-banner-content">
                            <div>
                                <h1 id="bannerTitle">Loading...</h1>
                                <p id="bannerSubtitle">...</p>
                            </div>
                            <button class="virtual-btn"><i class="fa-solid fa-video"></i> Virtual Class</button>
                        </div>
                    </div>
                    <div id="streamFeedArea" style="margin-top:30px;">Loading stream...</div>
                </div>

                <div id="classworkSection" class="active-tab-content" style="display:none;">
                    
                    <div id="streamListView">
                        <div class="action-header">
                            <h2>Classwork</h2>
                            <div style="position: relative;">
                                <button class="create-btn" onclick="toggleDropdown()">
                                    <i class="fa-solid fa-plus"></i> Create
                                </button>
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
        </main>
    </div>

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

    <div id="editClassModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Rename Class</h2><span style="cursor:pointer;" onclick="closeAllModals()">&times;</span></div>
            <div class="modal-body">
                <input type="hidden" id="editClassId">
                <div class="input-group"><label>Class Name</label><input type="text" id="editClassName"></div>
                <div class="input-group"><label>Section</label><input type="text" id="editClassSection"></div>
            </div>
            <div class="modal-footer"><button class="btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn-go" onclick="saveClassRename()">Save Changes</button></div>
        </div>
    </div>

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

    <div id="aiModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Generate Quiz</h2><span style="cursor:pointer" onclick="closeAllModals()">&times;</span></div>
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
            <div class="modal-footer"><button class="btn-cancel" onclick="closeAllModals()">Cancel</button><button class="btn-go" id="aiBtn" onclick="generateQuiz()">Generate</button></div>
        </div>
    </div>

<script>
    // --- SETUP ---
    const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
    let supabaseClient;
    const currentUser = { id: "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11" };
    let currentClassId = null;

    try { supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey); } 
    catch (e) { console.error(e); }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const cid = urlParams.get('class_id');
        if (cid) openClass(cid); 
        else fetchClasses(); 
    });

    // --- HELPER ---
    function showFile(input, txtId) { 
        if(input.files[0]) { 
            document.getElementById(txtId).innerHTML = `<i class="fa-solid fa-file-pdf"></i> <b>${input.files[0].name}</b>`; 
            document.getElementById(txtId).style.color = '#333'; 
        } 
    }

    // --- VIEW SWITCHING ---
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

    async function openClass(classId) {
    currentClassId = classId;
    
    // Add this line to ensure the Top Nav stays blue
    document.getElementById('nav-classes').classList.add('active');

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
        }
        fetchClasswork();
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

    // --- DATA FETCHING (UPDATED WITH BUTTONS) ---
    async function fetchClasses() {
        const grid = document.getElementById('classGrid');
        const loader = document.getElementById('loadingMsg');
        
        const { data } = await supabaseClient.from('classes')
            .select('*').eq('teacher_id', currentUser.id)
            .order('created_at', { ascending: false });

        loader.style.display = 'none';
        grid.innerHTML = '';

        if(!data || data.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#999;">No classes found. Create one!</div>';
            return;
        }

        const imgs = [
            "https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&w=800&q=80",
            "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&w=800&q=80",
            "https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&w=800&q=80"
        ];

        data.forEach((cls, idx) => {
            const card = document.createElement('div');
            card.className = 'class-card';
            
            // Buttons logic + Click wrapper for class open
            card.innerHTML = `
                <div class="card-actions">
                    <div class="action-icon icon-edit" onclick="openEditModal('${cls.id}', '${cls.title}', '${cls.section}')">
                        <i class="fa-solid fa-pen"></i>
                    </div>
                    <div class="action-icon icon-del" onclick="deleteClass('${cls.id}')">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                </div>
                <div onclick="openClass(${cls.id})">
                    <div class="class-image"><img src="${imgs[idx % imgs.length]}"></div>
                    <div class="class-text-content">
                        <div class="class-title">${cls.title}</div>
                        <div class="class-subtitle">${cls.section}</div>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    // --- NEW: EDIT & DELETE LOGIC ---
    function openEditModal(id, title, section) {
        document.getElementById('editClassId').value = id;
        document.getElementById('editClassName').value = title;
        document.getElementById('editClassSection').value = section;
        document.getElementById('editClassModal').style.display = 'flex';
    }

    async function saveClassRename() {
        const id = document.getElementById('editClassId').value;
        const title = document.getElementById('editClassName').value;
        const section = document.getElementById('editClassSection').value;

        if(!title) return alert("Class name required");

        const { error } = await supabaseClient.from('classes').update({ title, section }).eq('id', id);
        
        if(error) alert("Error: " + error.message);
        else {
            closeAllModals();
            fetchClasses();
        }
    }

    async function deleteClass(classId) {
        if(!confirm("Are you sure you want to delete this class? This cannot be undone.")) return;
        
        const { error } = await supabaseClient.from('classes').delete().eq('id', classId);
        
        if(error) alert("Error deleting class: " + error.message);
        else fetchClasses();
    }

    // --- FETCH CLASSWORK ---
    async function fetchClasswork() {
        const { data } = await supabaseClient.from('classwork')
            .select('*').eq('class_id', currentClassId)
            .order('created_at', { ascending: false });
            
        const list = document.getElementById('streamFeedArea');
        const items = document.getElementById('streamItemsArea');
        
        list.innerHTML = '';
        items.innerHTML = '';
        
        if(!data || data.length === 0) {
            const msg = '<div style="text-align:center; color:#999; padding:20px;">No content yet.</div>';
            list.innerHTML = msg;
            items.innerHTML = msg;
            return;
        }
        
        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'stream-item';
            div.onclick = () => showDetail(item);

            let icon = 'fa-file-lines';
            let color = 'icon-assign';
            if(item.type === 'quiz') { icon = 'fa-robot'; color = 'icon-quiz'; }
            
            div.innerHTML = `
                <div class="item-icon ${color}"><i class="fa-solid ${icon}"></i></div>
                <div class="item-content">
                    <div class="item-title">${item.title}</div>
                    <div class="item-meta">Posted ${new Date(item.created_at).toLocaleDateString()}</div>
                </div>
            `;
            
            const divClone = div.cloneNode(true);
            divClone.onclick = () => { switchTab('classwork'); showDetail(item); };

            items.appendChild(div);
            list.appendChild(divClone);
        });
    }

    // --- FETCH PEOPLE ---
    async function fetchPeople() {
        const teacherArea = document.getElementById('teacherListArea');
        const studentArea = document.getElementById('studentListArea');

        const { data: classData } = await supabaseClient.from('classes').select(`teacher_id, teacher:profiles!teacher_id (full_name)`).eq('id', currentClassId).single();

        if (classData) {
            const tName = Array.isArray(classData.teacher) ? classData.teacher[0]?.full_name : classData.teacher?.full_name;
            teacherArea.innerHTML = `
                <h2 style="color:#1a73e8; font-size: 30px; font-weight:400; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px;">Teachers</h2>
                <div style="display:flex; align-items:center; gap:15px; padding: 10px 0;">
                    <div style="width:40px; height:40px; background:#e0e0e0; border-radius:50%; display:flex; justify-content:center; align-items:center;"><i class="fa-solid fa-user"></i></div>
                    <span style="font-weight:500; font-size: 16px;">${tName || 'Unknown'}</span>
                </div>`;
        }

        const { data: students } = await supabaseClient.from('enrollments').select(`student:profiles (full_name)`).eq('class_id', currentClassId);

        let sHtml = `
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px;">
                <h2 style="color:#1a73e8; font-size: 30px; font-weight:400; margin:0;">Students</h2>
                <span style="color:#1a73e8; font-weight:500;">${students ? students.length : 0} students</span>
            </div>`;

        if (students && students.length > 0) {
            students.forEach(item => {
                const name = (Array.isArray(item.student) ? item.student[0]?.full_name : item.student?.full_name) || "Student";
                sHtml += `
                    <div style="display:flex; align-items:center; gap:15px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                        <div style="width:35px; height:35px; background:#1967d2; color:white; border-radius:50%; display:flex; justify-content:center; align-items:center; font-size:14px;">${name.charAt(0).toUpperCase()}</div>
                        <span style="font-weight:500; color:#3c4043;">${name}</span>
                    </div>`;
            });
        } else {
            sHtml += `<div style="color:#777; font-style:italic;">No students enrolled yet.</div>`;
        }
        studentArea.innerHTML = sHtml;
    }

    // --- SHOW DETAIL ---
    function showDetail(item) {
        document.getElementById('streamListView').style.display = 'none';
        document.getElementById('detailView').style.display = 'block';
        document.getElementById('detTitle').innerText = item.title;
        
        let content = `<p style="font-size:1.1rem; color:#555;">${item.description || ''}</p>`;
        
        if (item.due_date) {
            content += `<p style="color:#e37400; font-weight:500;"><i class="fa-regular fa-clock"></i> Due: ${new Date(item.due_date).toLocaleString()}</p>`;
        }

        if(item.type === 'quiz' && item.quiz_data) {
            let questions = item.quiz_data;
            if (typeof questions === 'string') { try { questions = JSON.parse(questions); } catch(e) {} }

            if (Array.isArray(questions)) {
                content += `<div style="margin-top:20px;">`;
                questions.forEach((q, i) => {
                    let opts = '';
                    if(Array.isArray(q.options)) {
                        opts = `<div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:10px;">
                            ${q.options.map(o => `<div style="background:#f8f9fa; padding:10px; border:1px solid #ddd; border-radius:4px;">${o}</div>`).join('')}
                        </div>`;
                    }
                    content += `
                        <div style="background:white; padding:20px; border:1px solid #eee; border-radius:8px; margin-bottom:15px;">
                            <div style="font-weight:600; font-size:16px;">${i+1}. ${q.question}</div>
                            ${opts}
                            <div style="margin-top:10px; color:#137333; font-weight:500;">Answer: ${q.answer}</div>
                        </div>`;
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

    // --- ACTIONS ---
    async function createClass() {
        const title = document.getElementById('newClassName').value;
        const section = document.getElementById('newClassSection').value;
        if(!title) return alert("Required");
        await supabaseClient.from('classes').insert([{ teacher_id: currentUser.id, title, section }]);
        closeAllModals();
        fetchClasses();
    }

    async function createAssign() {
        const title = document.getElementById('asTitle').value;
        const desc = document.getElementById('asInstr').value;
        const due = document.getElementById('asDueDate').value;
        if(!title) return alert("Required");
        await supabaseClient.from('classwork').insert([{
            teacher_id: currentUser.id, class_id: currentClassId, type: 'assignment', title, description: desc, due_date: due ? new Date(due).toISOString() : null
        }]);
        closeAllModals();
        fetchClasswork();
    }

    async function generateQuiz() {
        const file = document.getElementById('aiFile').files[0];
        const prompt = document.getElementById('aiPrompt').value;
        const btn = document.getElementById('aiBtn');
        if(!file) return alert("Upload PDF");
        
        btn.innerText = "Generating..."; btn.disabled = true;
        try {
            const fd = new FormData();
            fd.append('pdf_file', file);
            fd.append('custom_prompt', prompt);
            const res = await fetch('api/generate_quiz_api.php', { method:'POST', body:fd });
            const result = await res.json();
            
            if(result.success) {
                await supabaseClient.from('classwork').insert([{
                    teacher_id: currentUser.id, class_id: currentClassId, type: 'quiz', title: 'Quiz: ' + file.name, description: 'AI Generated', file_name: file.name, quiz_data: result.questions
                }]);
                closeAllModals();
                fetchClasswork();
            } else { alert(result.message); }
        } catch(e) { alert("Error generating quiz"); }
        finally { btn.innerText = "Generate"; btn.disabled = false; }
    }

    // --- MODALS ---
    function toggleDropdown() { 
        const d = document.getElementById('createDropdown');
        d.style.display = (d.style.display === 'block') ? 'none' : 'block';
    }
    function openClassModal() { document.getElementById('createClassModal').style.display = 'flex'; }
    function openAssignModal() { 
        document.getElementById('createDropdown').style.display='none';
        document.getElementById('assignModal').style.display = 'flex'; 
    }
    function openAiModal() {
        document.getElementById('createDropdown').style.display='none';
        document.getElementById('aiModal').style.display = 'flex';
    }
    function closeAllModals() { document.querySelectorAll('.modal-overlay').forEach(e => e.style.display = 'none'); }
    
    window.onclick = function(e) {
        if(e.target.classList.contains('modal-overlay')) closeAllModals();
        if (!e.target.matches('.create-btn') && !e.target.closest('.create-btn')) {
            const dropdown = document.getElementById('createDropdown');
            if (dropdown) dropdown.style.display = 'none';
        }
    };
    async function handleLogout() {
    if (confirm("Are you sure you want to log out?")) {
        try {
            // If using Supabase Auth
            if (supabaseClient.auth) {
                await supabaseClient.auth.signOut();
            }
            // Redirect to login page
            window.location.href = 'index.php';
        } catch (err) {
            console.error("Logout Error:", err);
            // Fallback redirect
            window.location.href = 'index.php';
        }
    }
}
</script>
</body>
</html>