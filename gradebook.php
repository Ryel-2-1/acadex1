<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechHub - Gradebook</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

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
    .main-content { flex: 1; padding: 30px 50px; overflow-y: auto; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .class-selector { padding: 10px 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px; min-width: 250px; }

    /* --- GRADEBOOK TABLE --- */
    .gradebook-container { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto; border: 1px solid #e0e0e0; padding-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; border-right: 1px solid #eee; vertical-align: middle; }
    th { background: #f8f9fa; font-weight: 600; color: #555; position: sticky; top: 0; z-index: 10; }
    th:first-child, td:first-child { position: sticky; left: 0; background: white; z-index: 11; border-right: 2px solid #e0e0e0; width: 220px; }
    th:first-child { background: #f8f9fa; z-index: 12; }
    
    .cell-wrapper { display: flex; align-items: center; gap: 8px; }
    .grade-input { width: 50px; padding: 5px; border: 1px solid transparent; text-align: center; border-radius: 4px; background: transparent; font-weight: 500; }
    .grade-input:hover { border-color: #ddd; background: #fff; }
    .grade-input:focus { border-color: #1a73e8; outline: none; background: #fff; }
    
    /* Status Icons */
    .status-icon { cursor: pointer; font-size: 14px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: 0.2s; }
    .status-submitted { color: #1a73e8; background: #e8f0fe; }
    .status-submitted:hover { background: #d2e3fc; }
    .status-missing { color: #d93025; font-size: 12px; }

    .student-row:hover { background-color: #fcfcfc; }
    .assignment-header { font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
    .date-sub { display: block; font-size: 11px; color: #999; font-weight: 400; margin-top: 4px; }

    /* Submission Modal */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 8px; width: 500px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .modal-body { margin-bottom: 20px; }
    .file-preview { padding: 15px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 6px; margin-top: 10px; word-break: break-all; }
    .btn-close { background: #eee; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }

    #loading-state { text-align: center; padding: 50px; color: #777; font-size: 18px; }
    #global-loader { position: fixed; inset:0; background:white; z-index:3000; display:flex; justify-content:center; align-items:center; font-size:18px; color:#666; }
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
            <a href="Classwork.php" class="nav-item">Classes</a>
            <a href="gradebook.php" class="nav-item active">Gradebook</a>
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

    <main class="main-content">
        <div class="page-header">
            <h2 style="margin:0; font-weight:400;">Gradebook</h2>
            <select id="classSelect" class="class-selector" onchange="loadGradebook()">
                <option value="" disabled selected>Select a Class</option>
            </select>
        </div>

        <div id="loading-state">Please select a class to view grades.</div>

        <div id="gradebook-wrapper" class="gradebook-container" style="display:none;">
            <table id="gradeTable">
                <thead>
                    <tr id="tableHeaderRow">
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </main>

    <div id="submissionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0;">Student Submission</h3>
                <span style="cursor:pointer; font-size:24px;" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                </div>
            <div style="text-align:right;">
                <button class="btn-close" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

<script>
    const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
    let supabaseClient;
    let currentUser = null;

    try { supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey); } 
    catch (e) { console.error(e); }

    document.addEventListener('DOMContentLoaded', async () => {
        const { data: { session } } = await supabaseClient.auth.getSession();
        if (!session) { window.location.href = 'teacher_login.php'; return; }
        currentUser = session.user;

        await fetchUserProfile();
        await fetchClasses();
        document.getElementById('global-loader').style.display = 'none';
    });

    async function fetchUserProfile() {
        const { data: profile } = await supabaseClient.from('profiles').select('full_name').eq('id', currentUser.id).single();
        if (profile) {
            document.getElementById('profile-name').innerText = profile.full_name;
            document.getElementById('profile-avatar').style.backgroundImage = `url('https://ui-avatars.com/api/?name=${encodeURIComponent(profile.full_name)}&background=0D8ABC&color=fff')`;
        }
    }

    async function fetchClasses() {
        const select = document.getElementById('classSelect');
        const { data: classes } = await supabaseClient.from('classes').select('id, title, section').eq('teacher_id', currentUser.id).order('created_at', { ascending: false });

        if (classes && classes.length > 0) {
            classes.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.id;
                opt.innerText = `${cls.title} (${cls.section})`;
                select.appendChild(opt);
            });
            select.value = classes[0].id;
            loadGradebook();
        } else {
            document.getElementById('loading-state').innerText = "No classes found.";
        }
    }

   async function loadGradebook() {
    const classId = document.getElementById('classSelect').value;
    const wrapper = document.getElementById('gradebook-wrapper');
    const loading = document.getElementById('loading-state');
    const theadRow = document.getElementById('tableHeaderRow');
    const tbody = document.getElementById('tableBody');

    if(!classId) return;

    // Show loading state
    wrapper.style.display = 'none'; 
    loading.style.display = 'block'; 
    loading.innerText = "Loading grades...";

    try {
        // 1. Fetch Students
        const { data: enrollments } = await supabaseClient
            .from('enrollments')
            .select('student_id, student:profiles!student_id(full_name)')
            .eq('class_id', classId);
        
        // 2. Fetch Assignments
        const { data: assignments } = await supabaseClient
            .from('classwork')
            .select('id, title, due_date')
            .eq('class_id', classId)
            .order('created_at', { ascending: true });

        // 3. FETCH SUBMISSIONS (This is now your source of grades)
        const assignmentIds = assignments.map(a => a.id);
        let submissionsMap = {};
        
        if (assignmentIds.length > 0) {
            const { data: subs } = await supabaseClient
                .from('submissions')
                .select('student_id, classwork_id, grade, status, content, file_url, created_at')
                .in('classwork_id', assignmentIds);
                
            // Map data so we can find it easily: "studentID-assignmentID" -> Submission Object
            if (subs) {
                subs.forEach(s => { 
                    submissionsMap[`${s.student_id}-${s.classwork_id}`] = s; 
                });
            }
        }

        // --- BUILD HEADER ---
        // We add an extra column for "Average"
        let headerHtml = '<th>Student Name</th><th style="background:#e8f0fe; color:#1a73e8; text-align:center;">Average</th>';
        assignments.forEach(a => {
            headerHtml += `<th><div class="assignment-header" title="${a.title}">${a.title}</div><span class="date-sub">${a.due_date ? new Date(a.due_date).toLocaleDateString() : '-'}</span></th>`;
        });
        theadRow.innerHTML = headerHtml;

        // --- BUILD BODY ---
        tbody.innerHTML = '';
        
        if (!enrollments || enrollments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="100%" style="text-align:center; padding:20px;">No students enrolled yet.</td></tr>';
        } else {
            enrollments.forEach(enr => {
                const tr = document.createElement('tr');
                tr.className = 'student-row';
                
                // --- CALCULATE AVERAGE based on SUBMISSIONS table ---
                let totalScore = 0;
                let gradedCount = 0;
                
                assignments.forEach(a => {
                    const key = `${enr.student_id}-${a.id}`;
                    const sub = submissionsMap[key];
                    
                    // Only count if submission exists AND has a grade
                    if (sub && sub.grade !== null) {
                        totalScore += parseInt(sub.grade);
                        gradedCount++;
                    }
                });

                const average = gradedCount > 0 ? Math.round(totalScore / gradedCount) : 0;
                // Green if 75+, Red if below
                const avgColor = average >= 75 ? '#137333' : '#d93025';

                let rowHtml = `
                    <td style="font-weight:500;">${enr.student ? enr.student.full_name : 'Unknown'}</td>
                    <td style="text-align:center; font-weight:bold; color:${avgColor}; background:#f8f9fa;">${average}%</td>
                `;

                // --- RENDER CELLS ---
                assignments.forEach(a => {
                    const key = `${enr.student_id}-${a.id}`;
                    const sub = submissionsMap[key];
                    
                    // Determine values to display
                    const score = (sub && sub.grade !== null) ? sub.grade : '';
                    let statusIcon = '<div class="status-icon status-missing" title="No work submitted"><i class="fa-solid fa-minus"></i></div>';

                    if (sub) {
                        // Sanitize content for the onClick function
                        const safeContent = sub.content ? sub.content.replace(/"/g, '&quot;') : '';
                        const safeFile = sub.file_url ? sub.file_url : '';
                        const safeDate = new Date(sub.created_at).toLocaleDateString();

                        if (sub.status === 'graded') {
                            // Green Check for Graded
                            statusIcon = `<div class="status-icon" style="color:#137333; background:#e6f4ea;" onclick='viewSubmission("${safeContent}", "${safeFile}", "${safeDate}")' title="View Submission"><i class="fa-solid fa-check"></i></div>`;
                        } else {
                            // Blue Eye for Submitted (Needs Grading)
                            statusIcon = `<div class="status-icon" style="color:#1a73e8; background:#e8f0fe;" onclick='viewSubmission("${safeContent}", "${safeFile}", "${safeDate}")' title="View Submission"><i class="fa-solid fa-eye"></i></div>`;
                        }
                    }

                    // Input is disabled here because grading happens inside the modal (via viewSubmission)
                    rowHtml += `<td>
                        <div class="cell-wrapper">
                            ${statusIcon}
                            <input type="number" class="grade-input" value="${score}" placeholder="-" disabled style="background:transparent; border:none; color:#333; cursor:default;">
                        </div>
                    </td>`;
                });
                
                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        // Hide loader, show table
        loading.style.display = 'none'; 
        wrapper.style.display = 'block';

    } catch (err) { 
        console.error(err); 
        loading.innerText = "Error loading data."; 
    }
}
    async function saveGrade(studentId, classworkId, value) {
        if (value === '') return;
        const { data: existing } = await supabaseClient.from('grades').select('id').eq('student_id', studentId).eq('classwork_id', classworkId).single();
        if (existing) {
            await supabaseClient.from('grades').update({ score: value }).eq('id', existing.id);
        } else {
            await supabaseClient.from('grades').insert([{ student_id: studentId, classwork_id: classworkId, score: value }]);
        }
    }

    function viewSubmission(content, fileUrl, date) {
        const body = document.getElementById('modalBody');
        let html = `<p><strong>Submitted on:</strong> ${date}</p>`;
        
        if (content) {
            html += `<div><strong>Student Answer:</strong><div class="file-preview">${content}</div></div>`;
        }
        if (fileUrl) {
            html += `<div style="margin-top:15px;"><strong>Attached File:</strong><br><a href="${fileUrl}" target="_blank" style="color:#1a73e8; text-decoration:underline;">Download / View File</a></div>`;
        }
        if (!content && !fileUrl) {
            html += `<p><em>Empty submission</em></p>`;
        }
        
        body.innerHTML = html;
        document.getElementById('submissionModal').style.display = 'flex';
    }

    function closeModal() { document.getElementById('submissionModal').style.display = 'none'; }
    window.onclick = function(e) { if(e.target.id === 'submissionModal') closeModal(); }

    async function handleLogout() {
        if (confirm("Logout?")) { await supabaseClient.auth.signOut(); window.location.href = 'teacher_login.php'; }
    }
</script>
</body>
</html>