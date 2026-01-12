<?php
session_start();
// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- General Reset --- */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; }
        * { box-sizing: border-box; }

        /* --- HEADER STYLES --- */
        header {
            background-color: white; padding: 0 40px; height: 70px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: sticky; top: 0; z-index: 1000;
        }

        .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; }
        .logo-icon { font-size: 32px; color: #1a73e8; }
        .logo-text { font-size: 24px; font-weight: 700; color: #000; }

        .nav-links { display: flex; gap: 30px; height: 100%; }
        .nav-item {
            display: flex; align-items: center; gap: 8px; text-decoration: none; color: #666;
            font-weight: 500; font-size: 16px; padding: 0 5px; position: relative; height: 100%;
            cursor: pointer;
        }
        .nav-item:hover, .nav-item.active { color: #1a73e8; }
        .nav-item.active::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 100%; height: 3px; background-color: #1a73e8;
        }

        /* --- PROFILE SECTION --- */
        .profile-section { 
            display: flex; align-items: center; gap: 12px; text-align: right; 
            width: 250px; justify-content: flex-end; position: relative; 
        }
        .profile-info h4 { margin: 0; font-size: 15px; font-weight: 600; color: #333; }
        .profile-info span { font-size: 13px; color: #777; display: block; }
        .avatar {
            width: 40px; height: 40px; background-color: #ddd; border-radius: 50%;
            background-image: url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff');
            background-size: cover; cursor: pointer;
        }

        /* --- DROPDOWNS --- */
        .dropdown-menu {
            display: none; position: absolute; background: white; 
            border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            padding: 8px 0; z-index: 1100; border: 1px solid #e0e0e0;
        }
        #profileDropdown { right: 0; left: auto; top: 60px; width: 180px; }
        #createDropdown { top: 50px; left: 0; width: 280px; }

        .dropdown-item {
            padding: 12px 20px; display: flex; align-items: center; gap: 15px;
            color: #444; cursor: pointer; transition: background 0.1s;
            text-decoration: none; font-size: 14px;
        }
        .dropdown-item:hover { background-color: #f5f5f5; }
        .dropdown-item i { color: #5f6368; width: 20px; text-align: center; }
        .dropdown-item.logout-item:hover { background-color: #fce8e6; color: #d93025; }
        .dropdown-item.logout-item:hover i { color: #d93025; }

        /* --- DASHBOARD LAYOUT --- */
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; position: relative; }
        .action-bar { margin-bottom: 20px; position: relative; }
        .create-btn {
            background-color: #1a73e8; color: white; border: none;
            padding: 10px 24px; border-radius: 24px;
            font-weight: 600; font-size: 1rem; cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: background 0.2s;
        }

        /* --- GRID & CLASS CARDS --- */
        .class-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .class-card { background: transparent; cursor: pointer; transition: transform 0.2s; }
        .class-card:hover { transform: translateY(-3px); }
        .class-image { width: 100%; height: 160px; background-color: #ddd; border-radius: 20px; overflow: hidden; margin-bottom: 8px; }
        .class-image img { width: 100%; height: 100%; object-fit: cover; }
        .class-title { font-size: 13px; font-weight: 700; text-transform: uppercase; color: #000; padding-left: 5px; }

        /* --- WIDGETS --- */
        .widgets-container { display: grid; grid-template-columns: 1.5fr 0.8fr 1fr; gap: 20px; align-items: start; }
        .widget-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e0e0e0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .widget-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .widget-header h3 { margin: 0; font-size: 18px; font-weight: 500; }

        /* --- STAT CARDS --- */
        .stats-column { display: flex; flex-direction: column; gap: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e0e0e0; display: flex; flex-direction: column; justify-content: center; }
        .stat-icon { font-size: 24px; color: #555; margin-bottom: 10px; }
        .stat-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .stat-number { font-size: 32px; font-weight: 700; color: #333; }

        /* --- CALENDAR --- */
        .calendar-card { background: white; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .calendar-header { background-color: #000; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .calendar-header h3 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; padding: 15px; background: white; }
        .date.today { background-color: #1a73e8; color: white; font-weight: bold; border-radius: 50%; }
    </style>
</head>
<body>

    <header>
        <div class="logo-section">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="nav-item active"><i class="fa-solid fa-border-all"></i> Dashboard</a>
            <a href="Classwork.php" class="nav-item"><i class="fa-solid fa-book"></i> Classes</a>
            <a href="gradebook.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Gradebook</a>
        </nav>
        <div class="profile-section">
            <div class="profile-info" onclick="toggleProfileDropdown()" style="cursor: pointer;">
                <h4>Prof. Jhomari Gandionco</h4>
                <span>Teacher <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i></span>
            </div>
            <div class="avatar" onclick="toggleProfileDropdown()" style="cursor: pointer;"></div>
            
            <div id="profileDropdown" class="dropdown-menu">
                <div class="dropdown-item" onclick="window.location.href='profile.php'"><i class="fa-solid fa-user"></i> My Profile</div>
                <div class="dropdown-item" onclick="window.location.href='settings.php'"><i class="fa-solid fa-gear"></i> Settings</div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                <div class="dropdown-item logout-item" onclick="window.location.href='teacher_login.php'">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="action-bar">
            <button class="create-btn" onclick="toggleCreateDropdown(event)">
                <i class="fa-solid fa-plus"></i> Create
            </button>
            <div class="dropdown-menu" id="createDropdown">
                <div class="dropdown-item"><i class="fa-solid fa-file-lines"></i> Assignment</div>
                <div class="dropdown-item"><i class="fa-solid fa-robot"></i> Generate Ai Quiz</div>
                <div class="dropdown-item"><i class="fa-solid fa-circle-question"></i> Question</div>
            </div>
        </div>

        <div class="class-grid">
            <div class="class-card" onclick="window.location.href='Classwork.php'">
                <div class="class-image"><img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Classroom"></div>
                <div class="class-title">CLASS BSIT 4-1 PROGRAMMING</div>
            </div>
            <div class="class-card">
                <div class="class-image"><img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Classroom"></div>
                <div class="class-title">CLASS BSIT 3-1 PROGRAMMING</div>
            </div>
            <div class="class-card">
                <div class="class-image"><img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Classroom"></div>
                <div class="class-title">CLASS BSIT 2-1 PROGRAMMING</div>
            </div>
        </div>

        <div class="widgets-container">
            <div class="widget-card">
                <div class="widget-header"><h3>Recent Student Activity</h3><a href="#" style="font-size: 13px; color: #999; text-decoration: none;">View All</a></div>
                <p style="color: #777; font-size: 14px;">No recent submissions found.</p>
            </div>

            <div class="stats-column">
                <div class="stat-card">
                    <i class="fa-regular fa-clock stat-icon"></i>
                    <div class="stat-label">Number of Upcoming</div>
                    <div class="stat-number">6</div>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-user-graduate stat-icon"></i>
                    <div class="stat-label">Number of Students</div>
                    <div class="stat-number">109</div>
                </div>
            </div>

            <div class="calendar-card">
                <div class="calendar-header"><h3 id="calMonth">MONTH</h3><span id="calYear">2026</span></div>
                <div class="calendar-grid" id="calGrid">
                    </div>
            </div>
        </div>
    </main>

    <script>
        function toggleProfileDropdown() {
            const pm = document.getElementById('profileDropdown');
            const cm = document.getElementById('createDropdown');
            if(cm) cm.style.display = 'none';
            pm.style.display = (pm.style.display === 'block') ? 'none' : 'block';
        }

        function toggleCreateDropdown(e) {
            e.stopPropagation();
            const cm = document.getElementById('createDropdown');
            const pm = document.getElementById('profileDropdown');
            if(pm) pm.style.display = 'none';
            cm.style.display = (cm.style.display === 'block') ? 'none' : 'block';
        }

        window.onclick = function(e) {
            if (!e.target.closest('.profile-section')) document.getElementById('profileDropdown').style.display = 'none';
            if (!e.target.closest('.action-bar')) document.getElementById('createDropdown').style.display = 'none';
        }

        // Calendar filler
        const date = new Date();
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById('calMonth').innerText = months[date.getMonth()];
        const grid = document.getElementById('calGrid');
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
        const lastDate = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
        for(let i=0; i<firstDay; i++) grid.appendChild(document.createElement('div'));
        for(let i=1; i<=lastDate; i++) {
            const d = document.createElement('div');
            d.innerText = i;
            d.style.padding = "8px";
            if(i === date.getDate()) d.className = "date today";
            grid.appendChild(d);
        }
    </script>
</body>
</html>