<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['status_login'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Mengambil data mata kuliah dari session
$data_mk = isset($_SESSION['data_mk']) ? $_SESSION['data_mk'] : [];

// Menambahkan baris ini agar materi terbaru berada di atas
$data_mk = array_reverse($data_mk, true);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Materi Kuliah - Full Feature</title>
    <style>
        :root { 
            --gradasi-transparan: linear-gradient(135deg, rgba(106, 17, 203, 0.8) 0%, rgba(37, 117, 252, 0.8) 100%); 
            --gradasi-glow: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            --warna-teks: #333;
            --lebar-maksimal: 1000px;
        }
        
        body::before {
            content: "";
            background-image: url('images/undip.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: blur(8px);
            -webkit-filter: blur(8px);
            transform: scale(1.1);
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
        }

        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            padding: 20px;
            min-height: 100vh;
        }
        
        .header-card { 
            background: var(--gradasi-transparan); 
            backdrop-filter: blur(10px);
            color: white; 
            padding: 20px 30px; 
            border-radius: 12px; 
            max-width: var(--lebar-maksimal); 
            margin: 0 auto 15px auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            display: flex; 
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-info h1 { margin: 0; font-size: 1.4rem; text-transform: uppercase; }
        .header-info p { margin: 5px 0 0 0; font-size: 0.9rem; opacity: 0.9; }

        .top-nav { display: flex; gap: 10px; }
        .btn-nav {
            color: white; text-decoration: none; border: 1px solid rgba(255,255,255,0.4);
            padding: 8px 18px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;
            background: rgba(255,255,255,0.1); transition: all 0.3s ease;
        }
        .btn-nav:hover { background: white; color: #6a11cb; }

        .search-container {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 2000;
        }

        #searchInput {
            width: 45px;
            height: 45px;
            padding: 0;
            text-align: center;
            font-size: 1.2rem;
            border-radius: 50%;
            border: 2px solid transparent;
            background: var(--gradasi-glow) padding-box, linear-gradient(135deg, #fff, #888) border-box;
            color: white;
            outline: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.5), 0 0 30px rgba(37, 117, 252, 0.3);
        }

        #searchInput:focus {
            width: 220px;
            border-radius: 25px;
            padding: 0 20px;
            text-align: left;
            cursor: text;
        }

        .container { max-width: var(--lebar-maksimal); margin: 0 auto; }

        .materi-wrapper {
            margin-bottom: 25px;
            transition: opacity 0.5s ease, transform 0.5s ease;
            position: relative;
        }

        .judul-mk { 
            background: var(--gradasi-transparan); 
            backdrop-filter: blur(10px);
            padding: 6px 25px; 
            color: white;
            border-radius: 10px; 
            margin-bottom: 4px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .judul-mk h3 { margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 12px; font-weight: normal; }

        .isi-materi {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: background 0.3s ease;
        }

        .content { 
            font-size: 1.1rem; 
            line-height: 1.4; 
            color: var(--warna-teks); 
            outline: none; 
            min-height: 20px;
        }

        /* --- CSS TOOLBAR TERSEMBUNYI --- */
        .floating-toolbar {
            display: none; /* Default sembunyi */
            background: white;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: absolute;
            right: 25px;
            top: 45px;
            z-index: 100;
            gap: 5px;
        }
        .floating-toolbar button {
            background: #f8f9fa; border: 1px solid #ddd; cursor: pointer;
            padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;
        }
        .floating-toolbar button:hover { background: #eee; }
        
        .btn-toggle-tools {
            cursor: pointer; background: rgba(255,255,255,0.2); border: none;
            color: white; border-radius: 50%; width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; transition: 0.3s;
        }
        .btn-toggle-tools:hover { background: rgba(255,255,255,0.4); transform: rotate(180deg); }

        /* Render List Style */
        .content ul, .content ol { padding-left: 25px; }

        #saveStatus {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8); color: white; padding: 12px 25px;
            border-radius: 30px; font-size: 0.85rem; display: none; z-index: 3000;

            background: rgba(0, 0, 0, 0.6); /* 0.6 adalah tingkat transparansi (60%) */
            backdrop-filter: blur(5px);    /* Tambahkan ini agar ada efek blur di belakangnya */
            color: white; 
            padding: 12px 25px;
            border-radius: 30px; 
            font-size: 0.85rem; 
            display: none; 
            z-index: 3000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .empty-state {
            text-align: center; padding: 50px; background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(10px); border-radius: 12px; color: #444; font-style: italic;
        }
    </style>
</head>
<body>
   
    <div id="saveStatus">Tersimpan...</div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="⌕" onkeyup="filterMateri()">
    </div>

    <div class="header-card">
        <div class="header-info">
            <h1>Rekapitulasi Materi</h1>
            <p>NIM: 21120124120025 | Teknik Komputer S1 | Semester 4</p>
        </div>
        <div class="top-nav">
            <a href="index.php" class="btn-nav">Home</a>
            <a href="dashboard.php" class="btn-nav">Dashboard</a>
        </div>
    </div>

    <div class="container" id="materiContainer">
        <?php 
        $ada_materi = false;
        if (!empty($data_mk)) {
            foreach ($data_mk as $nama => $detail): 
                if (!empty(trim($detail['materi']))): 
                    $ada_materi = true;
        ?>
                <div class="materi-wrapper" id="wrapper-<?php echo md5($nama); ?>">
                    <div class="judul-mk">
                        <h3 class="nama-mk">📚 <?php echo htmlspecialchars($nama); ?></h3>
                        <button class="btn-toggle-tools" onclick="toggleTools('tools-<?php echo md5($nama); ?>')">▼</button>
                    </div>

                    <div class="floating-toolbar" id="tools-<?php echo md5($nama); ?>">
                        <button onclick="execCmd('bold')">B</button>
                        <button onclick="execCmd('italic')">I</button>
                        <button onclick="execCmd('underline')">U</button>
                        <button onclick="execCmd('insertUnorderedList')">•</button>
                        <button onclick="execCmd('insertOrderedList')">1.</button>
                        <button onclick="execCmd('justifyLeft')">L</button>
                        <button onclick="execCmd('justifyCenter')">C</button>
                    </div>

                    <div class="isi-materi">
                        <div class="content format-render" 
                             contenteditable="true" 
                             onblur="updateMateri('<?php echo addslashes($nama); ?>', this, 'wrapper-<?php echo md5($nama); ?>')">
                            <?php echo $detail['materi']; ?>
                        </div>
                    </div>
                </div>
        <?php 
                endif;
            endforeach; 
        }

        if (!$ada_materi): 
        ?>
            <div class="empty-state">
                <p>Belum ada catatan materi yang disimpan.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Fungsi memunculkan/menyembunyikan toolbar
    function toggleTools(id) {
        let tool = document.getElementById(id);
        tool.style.display = (tool.style.display === 'flex') ? 'none' : 'flex';
    }

    // Fungsi format teks
    function execCmd(command) {
        document.execCommand(command, false, null);
    }

    // Render HTML mentah menjadi format visual saat halaman dimuat
    document.querySelectorAll('.format-render').forEach(el => {
        let doc = new DOMParser().parseFromString(el.innerHTML, 'text/html');
        el.innerHTML = doc.documentElement.textContent || doc.documentElement.innerText;
    });

    function filterMateri() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let wrappers = document.getElementsByClassName('materi-wrapper');
        for (let i = 0; i < wrappers.length; i++) {
            let title = wrappers[i].querySelector('.nama-mk').innerText.toLowerCase();
            wrappers[i].style.display = title.includes(input) ? "" : "none";
        }
    }

    function updateMateri(namaMK, element, wrapperId) {
        let materiBaru = element.innerHTML.trim(); // Gunakan innerHTML agar format tersimpan
        let statusBox = document.getElementById('saveStatus');
        let wrapper = document.getElementById(wrapperId);

        let formData = new FormData();
        formData.append('update_materi_live', 'true');
        formData.append('nama_mk', namaMK);
        formData.append('materi_baru', materiBaru);

        statusBox.innerText = "⏳ Menyimpan...";
        statusBox.style.display = "block";

        fetch('save_live_materi.php', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            statusBox.innerText = "Saved Successfully";
            setTimeout(() => { statusBox.style.display = "none"; }, 1500);
        });
    }
    </script>
</body>
</html>