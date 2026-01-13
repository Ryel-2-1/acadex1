<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- GLOBAL STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #ffffff; color: #333; min-height: 100vh; display: flex; flex-direction: column; }

        /* --- HEADER --- */
        header { height: 65px; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e0e0e0; background: white; position: fixed; top: 0; width: 100%; z-index: 1000; }
        .logo-area { display: flex; align-items: center; gap: 12px; cursor: pointer; }
        .logo-icon { font-size: 28px; color: #00C060; } 
        .logo-text { font-size: 22px; font-weight: 600; color: #202124; }
        
        .header-right { display: flex; align-items: center; gap: 20px; }
        .join-btn-header { background: transparent; border: 1px solid #5f6368; padding: 8px 16px; border-radius: 4px; font-weight: 500; cursor: pointer; transition: 0.2s; }
        .join-btn-header:hover { background-color: #f5f5f5; color: #00C060; border-color: #00C060; }

        .profile-pic { width: 35px; height: 35px; border-radius: 50%; background-color: #00C060; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; }

        /* --- LAYOUT --- */
        .app-container { display: flex; margin-top: 65px; height: calc(100vh - 65px); }
        .sidebar { width: 300px; padding: 12px 0; flex-shrink: 0; overflow-y: auto; border-right: 1px solid #f0f0f0; background: white; transition: 0.3s; }
        .main-content { flex-grow: 1; padding: 24px; overflow-y: auto; position: relative; }

        /* --- SIDEBAR ITEMS --- */
        .sidebar-item { display: flex; align-items: center; padding: 12px 24px; font-size: 14px; font-weight: 500; color: #3c4043; cursor: pointer; border-radius: 0 24px 24px 0; margin-bottom: 2px; transition: background 0.2s; }
        .sidebar-item:hover { background-color: #f5f5f5; }
        .sidebar-item.active { background-color: #e6f4ea; color: #137333; } 
        .sidebar-item i { margin-right: 18px; width: 20px; text-align: center; font-size: 18px; }
        .sidebar-divider { margin: 10px 0; border-top: 1px solid #e0e0e0; }
        .sub-item { padding-left: 24px; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* --- CARD GRID (HOME VIEW) --- */
        .section-title { font-size: 24px; font-weight: 400; color: #3c4043; margin-bottom: 20px; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
        
        .class-card { background: white; border: 1px solid #dadce0; border-radius: 8px; overflow: visible; display: flex; flex-direction: column; height: 280px; cursor: pointer; transition: box-shadow 0.2s; position: relative; }
        .class-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-header-bg { height: 100px; padding: 16px; color: white; background: linear-gradient(135deg, #00C060, #009c4d); position: relative; border-radius: 8px 8px 0 0; }
        .class-title { font-size: 20px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .class-subtitle { font-size: 14px; margin-top: 4px; opacity: 0.9; }
        .teacher-name { font-size: 12px; margin-top: 4px; opacity: 0.8; }
        .card-body { flex-grow: 1; padding: 16px; position: relative; }
        .card-avatar { width: 70px; height: 70px; border-radius: 50%; position: absolute; top: -35px; right: 16px; background-color: #1a73e8; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; border: 3px solid white; }
        .card-footer { height: 50px; border-top: 1px solid #dadce0; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px; position: relative; }

        /* --- SINGLE CLASS VIEW --- */
        .class-nav-tabs { display: flex; border-bottom: 1px solid #e0e0e0; margin-bottom: 0; padding: 0 24px; }
        .nav-tab { padding: 16px 24px; text-decoration: none; color: #5f6368; font-weight: 500; border-bottom: 3px solid transparent; cursor: pointer; }
        .nav-tab:hover { background-color: #f5f5f5; color: #202124; }
        .nav-tab.active-tab { color: #00C060; border-bottom-color: #00C060; }
        
        .class-content-area { max-width: 1000px; margin: 0 auto; padding: 24px; }

        /* Stream Banner */
        .class-banner { height: 240px; border-radius: 8px; background-image: url('https://gstatic.com/classroom/themes/img_read.jpg'); background-size: cover; background-position: center; position: relative; margin-bottom: 24px; }
        .class-banner-content { position: absolute; bottom: 20px; left: 25px; color: white; }
        .banner-title { font-size: 32px; font-weight: 600; }
        .banner-section { font-size: 16px; margin-top: 5px; }

        .stream-grid { display: grid; grid-template-columns: 200px 1fr; gap: 24px; }
        .upcoming-box { border: 1px solid #dadce0; border-radius: 8px; padding: 16px; background: white; height: fit-content; }
        
        /* Interactive Stream Post */
        .stream-post { border: 1px solid #dadce0; border-radius: 8px; background: white; padding: 16px; display: flex; align-items: center; gap: 16px; margin-bottom: 16px; cursor: pointer; transition: 0.2s; }
        .stream-post:hover { box-shadow: 0 1px 3px rgba(0,0,0,0.2); background-color: #fafafa; }
        .post-icon { width: 40px; height: 40px; border-radius: 50%; background-color: #00C060; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        
        /* Classwork Item */
        .cw-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e0e0e0; cursor: pointer; transition: 0.2s; }
        .cw-item:hover { background-color: #f5f5f5; padding-left: 10px; padding-right: 10px; }
        
        /* People */
        .people-header { color: #00C060; font-size: 30px; border-bottom: 1px solid #00C060; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px; display: flex; justify-content: space-between; align-items: center; }
        .person-row { display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .person-avatar { width: 32px; height: 32px; background: #00C060; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; }

        /* --- CARD MENU --- */
        .menu-btn { color: #5f6368; padding: 8px; border-radius: 50%; cursor: pointer; }
        .card-menu-dropdown { display: none; position: absolute; bottom: 40px; right: 10px; background: white; min-width: 160px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); border-radius: 4px; z-index: 100; padding: 8px 0; }
        .card-menu-dropdown.show { display: block; }
        .menu-option { padding: 10px 20px; color: #333; font-size: 14px; cursor: pointer; }
        .menu-option:hover { background-color: #f5f5f5; }

        /* --- MODALS --- */
        .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 8px; width: 400px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
        
        /* Assignment Detail Modal (Larger) */
        .assignment-modal { width: 700px; max-width: 90%; }
        .assignment-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .assignment-title { font-size: 24px; color: #00C060; }
        .assignment-meta { font-size: 13px; color: #555; margin-bottom: 20px; }
        .assignment-desc { font-size: 15px; line-height: 1.5; color: #333; margin-bottom: 30px; white-space: pre-wrap; }
        
        .submission-area { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .submission-header { font-weight: 600; margin-bottom: 10px; display: flex; justify-content: space-between; }
        .file-upload-box { border: 2px dashed #ccc; padding: 20px; text-align: center; background: white; cursor: pointer; color: #00C060; margin-top: 10px; }
        .file-upload-box:hover { border-color: #00C060; background: #f0fff4; }

        .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-cancel { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .btn-join, .btn-submit { background: #00C060; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .input-field { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .empty-state { grid-column: 1 / -1; text-align: center; color: #777; margin-top: 50px; }
    </style>
</head>
<body>

    <header>
        <div class="logo-area" onclick="window.location.href='student.php'">
            <i class="fa-solid fa-graduation-cap logo-icon"></i>
            <span class="logo-text">TechHub Student</span>
        </div>
        
        <div id="class-tabs-container" style="display:none; flex: 1; justify-content: center;">
            <div class="class-nav-tabs">
                <div class="nav-tab active-tab" id="tab-stream" onclick="switchClassTab('stream')">Stream</div>
                <div class="nav-tab" id="tab-classwork" onclick="switchClassTab('classwork')">Classwork</div>
                <div class="nav-tab" id="tab-people" onclick="switchClassTab('people')">People</div>
            </div>
        </div>

        <div class="header-right">
            <button class="join-btn-header" onclick="openJoinModal()"><i class="fa-solid fa-plus"></i> Join Class</button>
            <div class="profile-pic" id="headerInitials">S</div>
            <i class="fa-solid fa-right-from-bracket" onclick="handleLogout()" style="cursor: pointer; color: #5f6368; margin-left: 10px;" title="Logout"></i>
        </div>
    </header>

    <div class="app-container">
        
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-item active" id="nav-home" onclick="setView('home')">
                <i class="fa-solid fa-house"></i> Home
            </div>
            
            <div class="sidebar-divider"></div>
            <div class="sidebar-item" style="cursor: default; color: #5f6368;">
                <i class="fa-solid fa-graduation-cap"></i> Enrolled
            </div>
            <div id="sidebar-class-list"></div>

            <div class="sidebar-divider"></div>
            <div class="sidebar-item" id="nav-archive" onclick="setView('archived')">
                <i class="fa-solid fa-box-archive"></i> Archived Classes
            </div>
            <div class="sidebar-item" id="nav-unenroll" onclick="setView('unenrolled')">
                <i class="fa-solid fa-user-xmark"></i> Unenrolled Classes
            </div>
        </aside>

        <main class="main-content" id="main-content">
            </main>
    </div>

    <div id="joinModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="font-size: 20px; margin-bottom: 10px;">Join class</h2>
            <p style="font-size: 14px; color: #666;">Ask your teacher for the class code, then enter it here.</p>
            <input type="text" id="classCodeInput" class="input-field" placeholder="Class Code (e.g. 7X3J9A)">
            <div id="joinError" style="color: red; font-size: 13px; margin-top: 10px; display: none;"></div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeJoinModal()">Cancel</button>
                <button class="btn-join" id="btnJoinSubmit" onclick="joinClass()">Join</button>
            </div>
        </div>
    </div>

    <div id="assignmentModal" class="modal-overlay">
        <div class="modal-content assignment-modal">
            <div class="assignment-header">
                <div class="assignment-title" id="asDetailTitle">Assignment Title</div>
                <span style="cursor:pointer; font-size:24px;" onclick="closeAssignmentModal()">&times;</span>
            </div>
            <div class="assignment-meta" id="asDetailMeta">Posted on...</div>
            
            <div class="assignment-desc" id="asDetailDesc"></div>
            
            <div id="quizContainer" style="display:none; background:#f0f8ff; padding:20px; border-radius:8px; margin-bottom:20px;">
                </div>

            <div class="submission-area">
                <div class="submission-header">
                    <span>Your Work</span>
                    <span id="submissionStatus" style="color:#666; font-weight:400;">Assigned</span>
                </div>
                
                <div class="file-upload-box" onclick="document.getElementById('submitFile').click()">
                    <input type="file" id="submitFile" hidden onchange="handleFileSelect(this)">
                    <span id="fileNameDisplay"><i class="fa-solid fa-plus"></i> Add or Create</span>
                </div>
                
                <button class="btn-submit" style="width:100%; margin-top:15px;" id="btnMarkDone" onclick="submitAssignment()">Mark as Done</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
        const _supabase = supabase.createClient(supabaseUrl, supabaseKey);
        
        let currentUser = null;
        let allEnrollments = [];
        let currentView = 'home'; 
        let currentClassId = null; 
        let currentClassData = null; 
        let currentOpenItem = null; // Store currently opened assignment

        document.addEventListener('DOMContentLoaded', async () => {
            const { data: { session } } = await _supabase.auth.getSession();
            if (!session) { window.location.href = 'student_login.php'; return; }
            currentUser = session.user;
            document.getElementById('headerInitials').innerText = currentUser.email.charAt(0).toUpperCase();
            fetchEnrolledClasses();
        });

        async function fetchEnrolledClasses() {
            try {
                const { data, error } = await _supabase.from('enrollments')
                    .select(`class_id, classes:classes ( id, title, section, teacher_id, teacher:profiles ( full_name ) )`)
                    .eq('student_id', currentUser.id);
                if (error) throw error;
                allEnrollments = data || [];
                renderSidebar();
                if (currentView === 'home' || currentView === 'archived' || currentView === 'unenrolled') {
                    renderMainGrid();
                }
            } catch (err) { console.error(err); }
        }

        function renderSidebar() {
            const list = document.getElementById('sidebar-class-list');
            list.innerHTML = '';
            const activeClasses = allEnrollments.filter(item => getStatus(item.class_id) === 'active');

            activeClasses.forEach(item => {
                const cls = item.classes;
                if(!cls) return;
                const div = document.createElement('div');
                div.className = 'sidebar-item sub-item';
                if (currentView === 'class' && currentClassId == cls.id) {
                    div.classList.add('active');
                }
                div.innerHTML = `<i class="fa-regular fa-bookmark"></i> ${cls.title}`;
                div.onclick = () => openClass(cls.id);
                list.appendChild(div);
            });
        }

        // --- NAVIGATION LOGIC ---
        function setView(view) {
            currentView = view;
            currentClassId = null;
            document.getElementById('class-tabs-container').style.display = 'none';
            renderSidebar();
            renderMainGrid();
        }

        function renderMainGrid() {
            const main = document.getElementById('main-content');
            let titleText = "Home";
            let filtered = [];
            document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));

            if (currentView === 'home') {
                titleText = "Home";
                document.getElementById('nav-home').classList.add('active');
                filtered = allEnrollments.filter(item => getStatus(item.class_id) === 'active');
            } else if (currentView === 'archived') {
                titleText = "Archived Classes";
                document.getElementById('nav-archive').classList.add('active');
                filtered = allEnrollments.filter(item => getStatus(item.class_id) === 'archived');
            } else if (currentView === 'unenrolled') {
                titleText = "Unenrolled Classes";
                document.getElementById('nav-unenroll').classList.add('active');
                filtered = allEnrollments.filter(item => getStatus(item.class_id) === 'unenrolled');
            }

            let html = `<h2 class="section-title">${titleText}</h2><div class="card-grid">`;
            
            if (filtered.length === 0) {
                html += `<div class="empty-state"><i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 20px; color: #ccc;"></i><p>No classes found.</p></div>`;
            } else {
                filtered.forEach(item => {
                    const cls = item.classes;
                    const teacherName = cls.teacher ? cls.teacher.full_name : "Instructor";
                    const initial = teacherName.charAt(0).toUpperCase();
                    
                    let menu = '';
                    if (currentView === 'home') {
                         menu = `<div class="menu-option" onclick="setClassStatus('${item.class_id}', 'archived')">Archive</div>
                                 <div class="menu-option" onclick="setClassStatus('${item.class_id}', 'unenrolled')">Unenroll</div>`;
                    } else if (currentView === 'archived') {
                         menu = `<div class="menu-option" onclick="setClassStatus('${item.class_id}', 'active')">Restore</div>`;
                    } else {
                         menu = `<div class="menu-option" onclick="setClassStatus('${item.class_id}', 'active')">Re-enroll</div>`;
                    }
                    const style = currentView !== 'home' ? 'filter: grayscale(1);' : '';
                    const clickAction = currentView === 'home' ? `onclick="openClass(${cls.id})"` : '';

                    html += `
                    <div class="class-card" ${clickAction}>
                        <div class="card-header-bg" style="${style}">
                            <div class="class-title">${cls.title}</div>
                            <div class="class-subtitle">${cls.section}</div>
                            <div class="teacher-name">${teacherName}</div>
                        </div>
                        <div class="card-body">
                             <div class="card-avatar" style="${currentView!=='home'?'background:#777':''}">${initial}</div>
                        </div>
                        <div class="card-footer">
                            <i class="fa-regular fa-folder" style="color:#5f6368; margin-right:auto;"></i>
                            <i class="fa-solid fa-ellipsis-vertical menu-btn" onclick="toggleMenu(event, this)"></i>
                            <div class="card-menu-dropdown" onclick="event.stopPropagation()">${menu}</div>
                        </div>
                    </div>`;
                });
            }
            html += `</div>`;
            main.innerHTML = html;
        }

        // --- SINGLE CLASS LOGIC ---
        async function openClass(classId) {
            currentView = 'class';
            currentClassId = classId;
            const enrollment = allEnrollments.find(e => e.class_id == classId);
            if (!enrollment) return;
            currentClassData = enrollment.classes;

            document.getElementById('class-tabs-container').style.display = 'flex';
            renderSidebar();
            switchClassTab('stream');
        }

        function switchClassTab(tabName) {
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active-tab'));
            document.getElementById(`tab-${tabName}`).classList.add('active-tab');

            const main = document.getElementById('main-content');
            main.innerHTML = '<div style="text-align:center; padding:50px; color:#777;">Loading...</div>';

            if (tabName === 'stream') renderStream(main);
            else if (tabName === 'classwork') renderClasswork(main);
            else if (tabName === 'people') renderPeople(main);
        }

        async function renderStream(container) {
            const { data: posts } = await _supabase.from('classwork')
                .select('*').eq('class_id', currentClassId).order('created_at', {ascending: false});

            let postsHtml = '';
            if (posts && posts.length > 0) {
                posts.forEach(post => {
                    const icon = post.type === 'assignment' ? 'fa-clipboard-list' : 'fa-book-bookmark';
                    const date = new Date(post.created_at).toLocaleDateString();
                    // MAKE POST CLICKABLE: Calls openItem(post.id)
                    postsHtml += `
                    <div class="stream-post" onclick="openItem('${post.id}')">
                        <div class="post-icon"><i class="fa-solid ${icon}"></i></div>
                        <div>
                            <div style="font-weight:500; color:#3c4043;">Teacher posted a new ${post.type}: ${post.title}</div>
                            <div style="font-size:12px; color:#5f6368;">${date}</div>
                        </div>
                    </div>`;
                });
            } else {
                postsHtml = `<div style="text-align:center; color:#888; padding:20px;">No posts yet.</div>`;
            }

            container.innerHTML = `
                <div class="class-content-area">
                    <div class="class-banner">
                        <div class="class-banner-content">
                            <div class="banner-title">${currentClassData.title}</div>
                            <div class="banner-section">${currentClassData.section}</div>
                        </div>
                    </div>
                    <div class="stream-grid">
                        <div class="upcoming-box">
                            <div style="font-size:14px; font-weight:600; margin-bottom:10px;">Upcoming</div>
                            <div style="font-size:12px; color:#5f6368;">No work due soon</div>
                            <div style="margin-top:10px; text-align:right; font-size:13px; font-weight:500; color:#00C060; cursor:pointer;">View all</div>
                        </div>
                        <div class="stream-feed">
                            <div class="stream-post" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1); cursor: default;">
                                <div class="profile-pic" style="margin-right:15px;">S</div>
                                <div style="font-size:13px; color:#5f6368; flex-grow:1;">Announce something to your class</div>
                            </div>
                            ${postsHtml}
                        </div>
                    </div>
                </div>
            `;
        }

        async function renderClasswork(container) {
            const { data: works } = await _supabase.from('classwork')
                .select('*').eq('class_id', currentClassId).order('created_at', {ascending: false});

            let html = `<div class="class-content-area"><h2 style="margin-bottom:20px; font-weight:400; color:#00C060;">Classwork</h2>`;
            
            if(works && works.length > 0) {
                works.forEach(w => {
                    const icon = w.type === 'assignment' ? 'fa-clipboard-list' : 'fa-book-bookmark';
                    const color = w.type === 'assignment' ? '#00C060' : '#999';
                    // MAKE ITEM CLICKABLE: Calls openItem(w.id)
                    html += `
                    <div class="cw-item" onclick="openItem('${w.id}')">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <div style="width:36px; height:36px; background:${color}; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white;">
                                <i class="fa-solid ${icon}"></i>
                            </div>
                            <span style="font-weight:500;">${w.title}</span>
                        </div>
                        <div style="font-size:12px; color:#5f6368;">Posted ${new Date(w.created_at).toLocaleDateString()}</div>
                    </div>`;
                });
            } else {
                html += `<p style="color:#777;">No classwork assigned yet.</p>`;
            }
            html += `</div>`;
            container.innerHTML = html;
        }

        async function renderPeople(container) {
            const { data: students } = await _supabase.from('enrollments')
                .select(`student:profiles(full_name)`).eq('class_id', currentClassId);
            const teacherName = currentClassData.teacher ? currentClassData.teacher.full_name : "Unknown";

            let html = `<div class="class-content-area">`;
            html += `<div class="people-header">Teachers</div><div class="person-row"><div class="person-avatar"><i class="fa-solid fa-user"></i></div><span style="font-weight:500;">${teacherName}</span></div>`;
            html += `<div class="people-header">Classmates <span style="font-size:14px; color:#00C060;">${students?students.length:0} students</span></div>`;
            
            if (students && students.length > 0) {
                students.forEach(s => {
                    const name = s.student ? s.student.full_name : "Student";
                    html += `<div class="person-row"><div class="person-avatar" style="background:#1a73e8;"><i class="fa-solid fa-user"></i></div><span style="font-weight:500;">${name}</span></div>`;
                });
            } else {
                html += `<div style="font-style:italic; color:#777;">No other students yet.</div>`;
            }
            html += `</div>`;
            container.innerHTML = html;
        }

        // --- ASSIGNMENT DETAILS & SUBMISSION LOGIC ---
        async function openItem(itemId) {
            // 1. Fetch details
            const { data: item, error } = await _supabase.from('classwork').select('*').eq('id', itemId).single();
            if (error || !item) return alert("Error loading details");
            
            currentOpenItem = item;

            // 2. Populate Modal
            document.getElementById('asDetailTitle').innerText = item.title;
            const date = new Date(item.created_at).toLocaleString();
            document.getElementById('asDetailMeta').innerText = `Posted on ${date}`;
            document.getElementById('asDetailDesc').innerText = item.description || "No instructions provided.";

            // 3. Check for Quiz Data
            const quizDiv = document.getElementById('quizContainer');
            quizDiv.innerHTML = '';
            if (item.type === 'quiz' && item.quiz_data) {
                quizDiv.style.display = 'block';
                let qData = item.quiz_data;
                if(typeof qData === 'string') { try{qData = JSON.parse(qData)}catch(e){} }
                
                if (Array.isArray(qData)) {
                    let qHtml = '<h3>Quiz Questions</h3>';
                    qData.forEach((q, idx) => {
                         qHtml += `<div style="margin-bottom:15px; border-bottom:1px solid #ddd; padding-bottom:10px;">
                                    <b>${idx+1}. ${q.question}</b><br>
                                    <span style="color:#555">Answer: ${q.answer}</span>
                                   </div>`;
                    });
                    quizDiv.innerHTML = qHtml;
                }
            } else if (item.file_name) {
                // If it's a file upload assignment (PDF)
                quizDiv.style.display = 'block';
                quizDiv.innerHTML = `<i class="fa-solid fa-file-pdf"></i> Attached: <b>${item.file_name}</b>`;
            } else {
                quizDiv.style.display = 'none';
            }

            // 4. Reset Submission Box
            document.getElementById('fileNameDisplay').innerHTML = '<i class="fa-solid fa-plus"></i> Add or Create';
            document.getElementById('btnMarkDone').innerText = "Mark as Done";
            
            // 5. Open Modal
            document.getElementById('assignmentModal').style.display = 'flex';
        }

        function closeAssignmentModal() {
            document.getElementById('assignmentModal').style.display = 'none';
        }

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileNameDisplay').innerHTML = `<i class="fa-solid fa-file"></i> ${input.files[0].name}`;
                document.getElementById('btnMarkDone').innerText = "Submit";
            }
        }

        async function submitAssignment() {
            const btn = document.getElementById('btnMarkDone');
            btn.innerText = "Submitting..."; btn.disabled = true;

            // Simulate file upload (Since we don't have storage configured, we just save a record)
            try {
                const { error } = await _supabase.from('submissions').upsert({
                    student_id: currentUser.id,
                    classwork_id: currentOpenItem.id,
                    status: 'submitted',
                    grade: null // Reset grade if re-submitting
                }, { onConflict: 'student_id, classwork_id' });

                if (error) throw error;
                alert("Work submitted successfully!");
                closeAssignmentModal();

            } catch(e) {
                alert("Error submitting: " + e.message);
            } finally {
                btn.innerText = "Mark as Done"; btn.disabled = false;
            }
        }

        // --- UTILS ---
        function getStatus(classId) { return localStorage.getItem(`status_${currentUser.id}_${classId}`) || 'active'; }
        function setClassStatus(classId, status) {
            if (status === 'unenrolled' && !confirm("Unenroll?")) return;
            localStorage.setItem(`status_${currentUser.id}_${classId}`, status);
            fetchEnrolledClasses(); 
        }
        function toggleMenu(e, btn) {
            e.stopPropagation();
            const d = btn.nextElementSibling;
            document.querySelectorAll('.card-menu-dropdown').forEach(m => { if(m!==d) m.classList.remove('show'); });
            d.classList.toggle('show');
        }
        window.onclick = function(e) { 
            if(!e.target.matches('.menu-btn')) document.querySelectorAll('.card-menu-dropdown').forEach(m => m.classList.remove('show'));
            if(e.target == document.getElementById('joinModal')) closeJoinModal();
            if(e.target == document.getElementById('assignmentModal')) closeAssignmentModal();
        }
        function openJoinModal() { document.getElementById('joinModal').style.display = 'flex'; }
        function closeJoinModal() { document.getElementById('joinModal').style.display = 'none'; }
        async function joinClass() {
            const code = document.getElementById('classCodeInput').value.trim().toUpperCase();
            if(!code) return;
            document.getElementById('btnJoinSubmit').innerText="Joining...";
            try {
                const {data:c, error:e} = await _supabase.from('classes').select('id').eq('class_code', code).single();
                if(e || !c) throw new Error("Class not found");
                const {error:je} = await _supabase.from('enrollments').insert([{student_id:currentUser.id, class_id:c.id}]);
                if(je) throw je;
                localStorage.removeItem(`status_${currentUser.id}_${c.id}`); 
                alert("Joined!"); closeJoinModal(); fetchEnrolledClasses();
            } catch(err) {
                document.getElementById('joinError').innerText = err.message.includes('23505')?"Already joined":err.message;
                document.getElementById('joinError').style.display='block';
            } finally { document.getElementById('btnJoinSubmit').innerText="Join"; }
        }
        async function handleLogout() { await _supabase.auth.signOut(); window.location.href = 'student_login.php'; }
    </script>
</body>
</html>