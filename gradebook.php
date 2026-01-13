<?php
session_start();
// Security Check (Uncomment when login is ready)
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
    <title>TechHub - Gradebook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <style>
        /* --- GENERAL RESET --- */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; }
        * { box-sizing: border-box; }

        /* --- HEADER STYLES --- */
        header { background-color: white; padding: 0 40px; height: 70px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); position: sticky; top: 0; z-index: 100; }
        .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; cursor:pointer; }
        .logo-icon { font-size: 32px; color: #1a73e8; }
        .logo-text { font-size: 24px; font-weight: 700; color: #000; }
        .nav-links { display: flex; gap: 30px; height: 100%; }
        .nav-item { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #666; font-weight: 500; font-size: 16px; padding: 0 5px; position: relative; height: 100%; cursor: pointer; }
        .nav-item:hover, .nav-item.active { color: #1a73e8; }
        .nav-item.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background-color: #1a73e8; }
        .profile-section { display: flex; align-items: center; gap: 12px; text-align: right; width: 250px; justify-content: flex-end; }
        .profile-info h4 { margin: 0; font-size: 15px; font-weight: 600; color: #333; }
        .profile-info span { font-size: 13px; color: #777; display: block; }
        .avatar { width: 40px; height: 40px; background-color: #ddd; border-radius: 50%; background-image: url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff'); background-size: cover; }

        /* --- GRADEBOOK SPECIFIC STYLES --- */
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        .gb-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 15px 20px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .filter-group { display: flex; gap: 15px; align-items: center; flex: 1; }
        
        select.gb-select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; color: #444; outline: none; min-width: 250px; font-size: 14px; }

        .search-box { position: relative; }
        .search-box input { padding: 8px 12px 8px 35px; border: 1px solid #ddd; border-radius: 20px; font-family: inherit; outline: none; width: 100%; min-width: 250px; }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; }

        .export-btn { background-color: #27ae60; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .export-btn:hover { background-color: #219150; }

        /* Grade Table */
        .table-container { background: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.02); min-height: 400px; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        thead { background-color: #f8f9fa; border-bottom: 2px solid #e0e0e0; position: sticky; top: 0; z-index: 10; }
        th { text-align: left; padding: 15px 20px; font-size: 13px; font-weight: 700; color: #555; text-transform: uppercase; white-space: nowrap; border-right: 1px solid #eee; vertical-align: middle; }
        td { padding: 12px 20px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; border-right: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fcfcfc; }

        /* Sticky First Column */
        th:first-child, td:first-child { position: sticky; left: 0; background: white; z-index: 20; border-right: 2px solid #e0e0e0; width: 250px; }
        th:first-child { background: #f8f9fa; z-index: 30; }
        tr:hover td:first-child { background-color: #fcfcfc; }

        /* Student Cell Style */
        .student-cell { display: flex; align-items: center; gap: 12px; }
        .student-avatar { width: 32px; height: 32px; border-radius: 50%; background-color: #e3f2fd; color: #1976D2; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }

        /* Input Grade Cell */
        .grade-input { width: 60px; padding: 8px; border: 1px solid transparent; border-radius: 4px; text-align: center; font-family: inherit; background: transparent; transition: 0.2s; font-weight: 600; font-size: 14px; }
        .grade-input:hover { background: #f5f5f5; border-color: #ddd; }
        .grade-input:focus { background: white; border-color: #1a73e8; outline: none; box-shadow: 0 0 0 2px rgba(26,115,232,0.2); }

        .saving-status { font-size: 12px; color: #1a73e8; margin-left: 15px; display: none; font-weight: 600; align-items: center; gap: 5px; }
        
        #loadingMsg { text-align: center; padding: 60px; color: #777; font-size: 16px; font-style: italic; }
    </style>
</head>
<body>

    <header>
        <div class="logo-section" onclick="window.location.href='Dashboard.php'">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>

        <nav class="nav-links">
            <a href="Dashboard.php" class="nav-item">
                <i class="fa-solid fa-border-all"></i> Dashboard
            </a>
            <a href="Classwork.php" class="nav-item">
                <i class="fa-solid fa-book"></i> Classes
            </a>
            <a href="gradebook.php" class="nav-item active">
                <i class="fa-solid fa-graduation-cap"></i> Gradebook
            </a>
        </nav>

        <div class="profile-section">
            <div class="profile-info">
                <h4>Prof. Jhomari</h4>
                <span>Teacher</span>
            </div>
            <div class="avatar"></div>
        </div>
    </header>

    <main>
        
        <div class="gb-toolbar">
            <div class="filter-group">
                <label style="font-weight:600; font-size:14px;">Class:</label>
                <select class="gb-select" id="classSelector" onchange="loadGradebook(this.value)">
                    <option value="">-- Select Class to View Grades --</option>
                </select>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="studentSearch" placeholder="Search student..." onkeyup="filterTable()">
                </div>
                
                <span id="saveStatus" class="saving-status"><i class="fa-solid fa-spinner fa-spin"></i> Saving...</span>
            </div>

<button class="export-btn" onclick="exportToCSV()">
    <i class="fa-solid fa-file-csv"></i> Export CSV
</button>
        </div>

        <div class="table-container">
            <div id="loadingMsg">Select a class above to view grades.</div>
            
            <table id="gradeTable" style="display:none;">
                <thead>
                    <tr id="headerRow">
                        </tr>
                </thead>
                <tbody id="gradeBody">
                    </tbody>
            </table>
        </div>

    </main>

    <script>
        // --- SUPABASE CONFIG ---
        const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
        let supabaseClient;
        const currentUser = { id: "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11" }; // Hardcoded Teacher ID

        try { supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey); } 
        catch (e) { console.error("Init Error", e); }

        // --- INIT ---
        document.addEventListener('DOMContentLoaded', async () => {
            await loadClassList();
            
            // Auto-load if ID in URL
            const urlParams = new URLSearchParams(window.location.search);
            const classId = urlParams.get('class_id');
            if (classId) {
                const select = document.getElementById('classSelector');
                // Wait for options to populate before setting value
                // Since loadClassList is awaited above, we can set it now
                if (select.querySelector(`option[value="${classId}"]`)) {
                    select.value = classId;
                    loadGradebook(classId);
                }
            }
        });

        // --- 1. LOAD CLASSES ---
        async function loadClassList() {
            const { data, error } = await supabaseClient
                .from('classes')
                .select('id, title, section')
                .eq('teacher_id', currentUser.id)
                .order('created_at', { ascending: false });

            const select = document.getElementById('classSelector');
            
            if (error) { console.error(error); return; }
            
            if (data && data.length > 0) {
                data.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.innerText = `${cls.title} (${cls.section})`;
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="">No classes found</option>';
            }
        }

        // --- 2. LOAD GRADEBOOK ---
        async function loadGradebook(classId) {
            if(!classId) return;
            
            const loader = document.getElementById('loadingMsg');
            const table = document.getElementById('gradeTable');
            
            table.style.display = 'none';
            loader.style.display = 'block';
            loader.innerText = "Loading students and assignments...";

            try {
                // A. Get Assignments (Columns)
                const { data: assignments, error: assignError } = await supabaseClient
                    .from('classwork')
                    .select('id, title, type')
                    .eq('class_id', classId)
                    .in('type', ['assignment', 'quiz']) // Only gradable items
                    .order('created_at', { ascending: true }); // Oldest to newest (left to right)

                if (assignError) throw assignError;

                // B. Get Students (Rows)
                const { data: enrollments, error: enrollError } = await supabaseClient
                    .from('enrollments')
                    .select('student:profiles(id, full_name, email)')
                    .eq('class_id', classId);

                if (enrollError) throw enrollError;

                // Sort students manually by name
                if (enrollments) {
                    enrollments.sort((a, b) => 
                        (a.student?.full_name || "").localeCompare(b.student?.full_name || "")
                    );
                }

                // C. Get Grades
                let grades = [];
                if (assignments.length > 0) {
                    const assignmentIds = assignments.map(a => a.id);
                    const { data: gradeData } = await supabaseClient
                        .from('submissions')
                        .select('student_id, classwork_id, grade')
                        .in('classwork_id', assignmentIds);
                    grades = gradeData || [];
                }

                renderTable(enrollments, assignments, grades);

            } catch (err) {
                console.error("Error:", err);
                loader.innerText = "Error loading data. Please refresh.";
            }
        }

        // --- 3. RENDER TABLE ---
        function renderTable(enrollments, assignments, grades) {
            const loader = document.getElementById('loadingMsg');
            const table = document.getElementById('gradeTable');
            const headerRow = document.getElementById('headerRow');
            const body = document.getElementById('gradeBody');

            loader.style.display = 'none';
            table.style.display = 'table';
            headerRow.innerHTML = '';
            body.innerHTML = '';

            // --- HEADERS (Columns) ---
            const thName = document.createElement('th');
            thName.innerText = "Student Name";
            headerRow.appendChild(thName);

            if (!assignments || assignments.length === 0) {
                const th = document.createElement('th');
                th.innerText = "No Assignments Yet";
                th.style.fontWeight = "400";
                th.style.color = "#999";
                headerRow.appendChild(th);
            } else {
                assignments.forEach(a => {
                    const th = document.createElement('th');
                    // Icon logic
                    const icon = a.type === 'quiz' ? '<i class="fa-solid fa-robot"></i>' : '<i class="fa-solid fa-file-pen"></i>';
                    th.innerHTML = `${icon} <br>${a.title}`;
                    th.style.textAlign = "center";
                    headerRow.appendChild(th);
                });
            }

            // --- ROWS (Students) ---
            if (!enrollments || enrollments.length === 0) {
                body.innerHTML = '<tr><td colspan="100" style="padding:40px; text-align:center; color:#777;">No students enrolled in this class.</td></tr>';
                return;
            }

            enrollments.forEach(enr => {
                const student = enr.student;
                if(!student) return;

                const tr = document.createElement('tr');

                // 1. Student Name Cell
                const tdName = document.createElement('td');
                const initials = student.full_name ? student.full_name.substring(0,2).toUpperCase() : "??";
                tdName.innerHTML = `
                    <div class="student-cell">
                        <div class="student-avatar">${initials}</div>
                        <div>
                            <div style="font-weight:600;">${student.full_name}</div>
                            <div style="font-size:11px; color:#888;">${student.email || ''}</div>
                        </div>
                    </div>`;
                tr.appendChild(tdName);

                // 2. Grade Input Cells
                if (assignments.length > 0) {
                    assignments.forEach(assign => {
                        const existing = grades.find(g => g.student_id === student.id && g.classwork_id === assign.id);
                        const val = existing ? existing.grade : '';

                        const td = document.createElement('td');
                        td.style.textAlign = 'center';
                        td.innerHTML = `<input type="number" class="grade-input" 
                                        placeholder="-" value="${val}" 
                                        onchange="saveGrade(this, '${assign.id}', '${student.id}')">`;
                        tr.appendChild(td);
                    });
                } else {
                    tr.appendChild(document.createElement('td')); // Empty cell filler
                }

                body.appendChild(tr);
            });
        }

        // --- 4. SAVE GRADE ---
        async function saveGrade(input, classworkId, studentId) {
            const val = input.value;
            const statusEl = document.getElementById('saveStatus');
            
            statusEl.style.display = 'inline-flex';
            statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            input.style.backgroundColor = "#fff";

            try {
                const grade = val === '' ? null : parseInt(val);

                const { error } = await supabaseClient
                    .from('submissions')
                    .upsert({ 
                        classwork_id: classworkId, 
                        student_id: studentId, 
                        grade: grade,
                        status: 'graded'
                    }, { onConflict: 'classwork_id, student_id' });

                if (error) throw error;

                // Success
                input.style.borderColor = "#27ae60";
                input.style.backgroundColor = "#e8f5e9";
                statusEl.innerHTML = '<i class="fa-solid fa-check"></i> Saved';
                statusEl.style.color = "#27ae60";
                
                setTimeout(() => { 
                    input.style.borderColor = "transparent"; 
                    input.style.backgroundColor = "transparent";
                    statusEl.style.display = 'none';
                    statusEl.style.color = "#1a73e8"; 
                }, 1500);

            } catch (err) {
                console.error("Save failed:", err);
                input.style.borderColor = "#c62828";
                input.style.backgroundColor = "#ffebee";
                alert("Failed to save grade.");
            }
        }

        // --- 5. SEARCH FILTER ---
        function filterTable() {
            const filter = document.getElementById('studentSearch').value.toUpperCase();
            const trs = document.getElementById('gradeTable').getElementsByTagName('tr');
            
            for (let i = 1; i < trs.length; i++) {
                const td = trs[i].getElementsByTagName('td')[0];
                if (td) {
                    const txt = td.textContent || td.innerText;
                    trs[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }
        // --- 6. EXPORT TO CSV ---
function exportToCSV() {
    const table = document.getElementById('gradeTable');
    if (!table || table.style.display === 'none') {
        alert("Please select a class and wait for grades to load before exporting.");
        return;
    }

    let csvContent = "";
    const rows = table.querySelectorAll("tr");

    rows.forEach((row) => {
        const rowData = [];
        const cols = row.querySelectorAll("th, td");

        cols.forEach((col, index) => {
            let data = "";
            
            // If it's the first column (Student Name), we need to extract text carefully
            if (index === 0) {
                // Gets the full name text from the student-cell div
                data = col.querySelector('div[style*="font-weight:600"]') ? 
                       col.querySelector('div[style*="font-weight:600"]').innerText : 
                       col.innerText;
            } else {
                // Check if there is an input (grade), otherwise take innerText
                const input = col.querySelector('input');
                data = input ? input.value : col.innerText;
            }

            // Clean data: remove newlines and escape double quotes
            data = data.replace(/\n/g, ' ').replace(/"/g, '""');
            
            // Wrap in quotes to handle commas within the data
            rowData.push(`"${data}"`);
        });

        csvContent += rowData.join(",") + "\n";
    });

    // Create a download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    
    // Create filename based on class name or timestamp
    const className = document.getElementById('classSelector').selectedOptions[0].text;
    const filename = `Grades_${className.replace(/[/\\?%*:|"<>]/g, '-')}.csv`;

    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
    </script>
</body>
</html>