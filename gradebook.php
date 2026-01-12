<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Gradebook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- GENERAL RESET --- */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; }
        * { box-sizing: border-box; }

        /* --- HEADER STYLES (Identical to Dashboard) --- */
        header {
            background-color: white;
            padding: 0 40px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: sticky; top: 0; z-index: 100;
        }

        .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; }
        .logo-icon { font-size: 32px; color: #1a73e8; }
        .logo-text { font-size: 24px; font-weight: 700; color: #000; }

        .nav-links { display: flex; gap: 30px; height: 100%; }
        
        .nav-item {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; color: #666;
            font-weight: 500; font-size: 16px;
            padding: 0 5px; position: relative; height: 100%;
            cursor: pointer;
        }

        .nav-item:hover { color: #1a73e8; }
        .nav-item.active { color: #1a73e8; }
        .nav-item.active::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 100%; height: 3px; background-color: #1a73e8;
        }

        .profile-section { display: flex; align-items: center; gap: 12px; text-align: right; width: 250px; justify-content: flex-end; }
        .profile-info h4 { margin: 0; font-size: 15px; font-weight: 600; color: #333; }
        .profile-info span { font-size: 13px; color: #777; display: block; }
        .avatar {
            width: 40px; height: 40px; background-color: #ddd;
            border-radius: 50%;
            background-image: url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff');
            background-size: cover;
        }

        /* --- GRADEBOOK SPECIFIC STYLES --- */
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        /* Toolbar */
        .gb-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .filter-group { display: flex; gap: 15px; align-items: center; }
        
        select.gb-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            color: #444;
            outline: none;
        }

        .search-box {
            position: relative;
        }
        .search-box input {
            padding: 8px 12px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-family: inherit;
            outline: none;
            width: 250px;
        }
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        .export-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .export-btn:hover { background-color: #219150; }

        /* Grade Table */
        .table-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow-x: auto; /* Allow side scroll if many columns */
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }

        th {
            text-align: left;
            padding: 15px 20px;
            font-size: 13px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            white-space: nowrap;
        }

        td {
            padding: 12px 20px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fcfcfc; }

        /* Student Cell Style */
        .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .student-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background-color: #e0e0e0;
            font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #666; font-weight: bold;
        }

        /* Input Grade Cell */
        .grade-input {
            width: 50px;
            padding: 6px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: center;
            font-family: inherit;
            background: transparent;
            transition: 0.2s;
        }
        .grade-input:hover, .grade-input:focus {
            background: #fff;
            border-color: #1a73e8;
            outline: none;
        }

        /* Status Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .pass { background-color: #e8f5e9; color: #27ae60; }
        .fail { background-color: #ffebee; color: #c0392b; }
        
        .average-cell {
            font-weight: 700;
            color: #1a73e8;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-section">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>

        <nav class="nav-links">
            <a href="dashboard.php" class="nav-item">
                <i class="fa-solid fa-border-all"></i> Dashboard
            </a>
            <a href="classwork.php" class="nav-item">
                <i class="fa-solid fa-book"></i> Classes
            </a>
            <a href="gradebook.php" class="nav-item active">
                <i class="fa-solid fa-graduation-cap"></i> Gradebook
            </a>
        </nav>

        <div class="profile-section">
            <div class="profile-info">
                <h4>Prof. Jhomari Gandionco</h4>
                <span>Teacher</span>
            </div>
            <div class="avatar"></div>
        </div>
    </header>

    <main>
        
        <div class="gb-toolbar">
            <div class="filter-group">
                <label style="font-weight:600; font-size:14px;">Class:</label>
                <select class="gb-select" id="classSelector">
                    <option value="1">BSIT 4-1 Programming</option>
                    <option value="2">BSIT 3-1 Programming</option>
                    <option value="3">BSIT 2-1 Programming</option>
                </select>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="studentSearch" placeholder="Search student..." onkeyup="filterTable()">
                </div>
            </div>

            <button class="export-btn">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </button>
        </div>

        <div class="table-container">
            <table id="gradeTable">
                <thead>
                    <tr>
                        <th style="width: 250px;">Student Name</th>
                        <th>Quiz 1 (100)</th>
                        <th>Quiz 2 (50)</th>
                        <th>Midterm (100)</th>
                        <th>Final Project (100)</th>
                        <th>Total Average</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar">JD</div>
                                <div>
                                    <div style="font-weight:600;">John Doe</div>
                                    <div style="font-size:12px; color:#888;">2023-00123</div>
                                </div>
                            </div>
                        </td>
                        <td><input type="number" class="grade-input" value="85" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="45" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="90" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="92" onchange="calculateRow(this)"></td>
                        <td class="average-cell">89%</td>
                        <td><span class="badge pass">Passed</span></td>
                    </tr>

                    <tr>
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar" style="background:#fff3e0; color:#e67e22;">AS</div>
                                <div>
                                    <div style="font-weight:600;">Alice Smith</div>
                                    <div style="font-size:12px; color:#888;">2023-00456</div>
                                </div>
                            </div>
                        </td>
                        <td><input type="number" class="grade-input" value="70" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="30" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="75" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="80" onchange="calculateRow(this)"></td>
                        <td class="average-cell">72%</td>
                        <td><span class="badge fail">Failed</span></td>
                    </tr>

                    <tr>
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar" style="background:#e3f2fd; color:#1976D2;">RJ</div>
                                <div>
                                    <div style="font-weight:600;">Robert Johnson</div>
                                    <div style="font-size:12px; color:#888;">2023-00789</div>
                                </div>
                            </div>
                        </td>
                        <td><input type="number" class="grade-input" value="95" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="48" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="98" onchange="calculateRow(this)"></td>
                        <td><input type="number" class="grade-input" value="95" onchange="calculateRow(this)"></td>
                        <td class="average-cell">96%</td>
                        <td><span class="badge pass">Passed</span></td>
                    </tr>
                    </tbody>
            </table>
        </div>

    </main>

    <script>
        // Simple search filter function
        function filterTable() {
            const input = document.getElementById("studentSearch");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("gradeTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { // Start at 1 to skip header
                const td = tr[i].getElementsByTagName("td")[0]; // Check Name column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        // Mock function to simulate grade calculation UI update
        // In the real app, this would send an AJAX request to save to DB
        function calculateRow(input) {
            // Visual feedback that data "saved"
            input.style.backgroundColor = "#e8f5e9";
            setTimeout(() => {
                input.style.backgroundColor = "transparent";
            }, 500);
            
            // Note: Actual average calculation logic would go here
            console.log("Grade updated: " + input.value);
        }
    </script>
</body>
</html>