<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GS Assurance | Déclaration de sinistre</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F2F2F2;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: #2E2E2E;
        }
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: radial-gradient(circle at 20% 30%, #6FAF4C, #A67C52, #F2F2F2, #6FAF4C);
            background-size: 300% 300%;
            animation: radialShift 14s ease infinite alternate;
        }
        @keyframes radialShift {
            0% { background-position: 0% 0%; background-size: 300% 300%; }
            50% { background-position: 100% 100%; background-size: 400% 400%; }
            100% { background-position: 50% 50%; background-size: 300% 300%; }
        }
        .floating-shape {
            position: fixed;
            background: rgba(111, 175, 76, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
            animation: floatComplex 20s infinite alternate ease-in-out;
        }
        .shape1 { width: 350px; height: 350px; top: -120px; left: -100px; background: radial-gradient(circle, rgba(111,175,76,0.3), rgba(166,124,82,0.2)); animation-duration: 24s; }
        .shape2 { width: 480px; height: 480px; bottom: -180px; right: -140px; background: radial-gradient(circle, rgba(166,124,82,0.3), rgba(111,175,76,0.2)); animation-duration: 28s; animation-direction: alternate-reverse; }
        .shape3 { width: 220px; height: 220px; top: 35%; right: 2%; background: rgba(242,242,242,0.25); border-radius: 40% 60% 60% 40% / 40% 50% 50% 60%; animation: morphing 12s infinite alternate, floatComplex 18s infinite; }
        .shape4 { width: 170px; height: 170px; bottom: 12%; left: 2%; background: rgba(111,175,76,0.2); border-radius: 30% 70% 70% 30% / 30% 40% 60% 70%; animation: morphing 15s infinite alternate-reverse, floatComplex 22s infinite; }
        @keyframes floatComplex {
            0% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-50px) rotate(12deg) scale(1.08); }
            100% { transform: translateY(30px) rotate(-8deg) scale(0.95); }
        }
        @keyframes morphing {
            0% { border-radius: 40% 60% 60% 40% / 40% 50% 50% 60%; }
            100% { border-radius: 60% 40% 40% 60% / 50% 40% 60% 50%; }
        }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2; }
        .navbar {
            background: rgba(242, 242, 242, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 0 0 36px 36px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 10;
            margin-bottom: 2rem;
        }
        .nav-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            flex-wrap: wrap;
        }
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-circle {
            background: #6FAF4C;
            width: 50px;
            height: 50px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px rgba(111,175,76,0.3);
        }
        .logo-circle span {
            font-size: 26px;
            font-weight: 800;
            color: white;
        }
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #A67C52;
        }
        .logo-text p {
            font-size: 0.7rem;
            color: #6FAF4C;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .nav-links {
            display: flex;
            gap: 1.8rem;
            list-style: none;
            align-items: center;
        }
        .nav-links a {
            text-decoration: none;
            font-weight: 700;
            color: #4A3B2C;
            transition: 0.2s;
        }
        .nav-links a:hover {
            color: #6FAF4C;
        }
        .btn-nav {
            background: #6FAF4C;
            color: white !important;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 700;
        }
        .btn-nav-outline {
            border: 1.5px solid #6FAF4C;
            color: #6FAF4C !important;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 700;
        }
        .form-card {
            background: rgba(242, 242, 242, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 48px;
            padding: 2rem;
            margin: 1.5rem 0 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(166,124,82,0.2);
        }
        .form-card h2 {
            color: #A67C52;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .form-card p {
            color: #6FAF4C;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: #4A3B2C;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 16px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            transition: 0.2s;
            background: white;
        }
        input:focus, textarea:focus {
            border-color: #6FAF4C;
            outline: none;
            box-shadow: 0 0 0 3px rgba(111,175,76,0.2);
        }
        .row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .row .form-group {
            flex: 1;
        }
        .btn-submit {
            background: #6FAF4C;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            background: #5A9A3A;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #A67C52;
            text-decoration: none;
            font-weight: 600;
        }
        footer {
            background: #A67C52;
            color: white;
            padding: 2rem 0 1rem;
            border-radius: 32px 32px 0 0;
            margin-top: 2rem;
        }
        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .footer-col h4 { margin-bottom: 0.8rem; font-weight: 800; }
        .footer-col a, .footer-col p { color: #F2F2F2; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .social i { font-size: 1.3rem; margin-right: 0.8rem; transition: 0.2s; }
        .copyright { text-align: center; padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.75rem; font-weight: 600; }
        @media (max-width: 800px) {
            .nav-flex { flex-direction: column; gap: 1rem; }
            .row { flex-direction: column; gap: 0; }
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <style>
        .location-wrapper {
            position: relative;
        }
        .location-input-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .location-input-row input {
            flex: 1;
        }
        #btn-geolocate {
            background: #6FAF4C;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            font-size: 1rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            transition: 0.2s;
            height: 46px;
        }
        #btn-geolocate:hover { background: #5A9A3A; }
        #btn-geolocate.loading { background: #A67C52; cursor: wait; }
        #autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 56px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            z-index: 9999;
            max-height: 220px;
            overflow-y: auto;
        }
        .autocomplete-item {
            padding: 10px 14px;
            cursor: pointer;
            font-size: 0.88rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .autocomplete-item:last-child { border-bottom: none; }
        .autocomplete-item:hover { background: #f7f7f7; }
        .autocomplete-item i { color: #6FAF4C; margin-top: 2px; flex-shrink: 0; }
        #map {
            height: 250px;
            margin-top: 10px;
            border-radius: 16px;
            border: 2px solid #e0e0e0;
            overflow: hidden;
        }
        #coords-display {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #888;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #coords-display i { color: #6FAF4C; }
    </style>
</head>
<body>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>
<div class="floating-shape shape3"></div>
<div class="floating-shape shape4"></div>

<div class="navbar">
    <div class="container nav-flex">
        <div class="logo-area">
            <div class="logo-circle"><span>GS</span></div>
            <div class="logo-text"><h1>GS Assurance</h1><p>CONFIANCE & STABILITÉ</p></div>
        </div>
        <ul class="nav-links">
            <li><a href="index.php?action=home">Accueil</a></li>
            <li><a href="index.php?action=demande">Déclaration</a></li>
            <li><a href="#">Assurance Auto</a></li>
            <li><a href="#" class="btn-nav-outline"><i class="fas fa-user"></i> Espace client</a></li>
            <li><a href="index.php?action=historique" class="btn-nav">Suivre un dossier</a></li>
        </ul>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <h2><i class="fas fa-car-crash"></i> Déclaration de sinistre</h2>
        <p>Formulaire en ligne – réponse sous 48h</p>

        <form action="index.php?action=soumettre" method="POST">
            <div class="row">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" required placeholder="Votre nom">
                </div>
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" required placeholder="Votre prénom">
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required placeholder="exemple@email.com">
                </div>
                <div class="form-group">
                    <label>Téléphone *</label>
                    <input type="tel" name="telephone" required placeholder="+216 XX XXX XXX">
                </div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt" style="color:#6FAF4C;margin-right:5px;"></i>Lieu de l'accident *</label>
                <div class="location-wrapper">
                    <div class="location-input-row">
                        <input id="lieu_accident" type="text" name="lieu_accident" required placeholder="Tapez une adresse ou utilisez votre position..." autocomplete="off">
                        <button type="button" id="btn-geolocate" title="Utiliser ma position GPS">
                            <i class="fas fa-crosshairs"></i> Ma position
                        </button>
                    </div>
                    <div id="autocomplete-list" style="display:none;"></div>
                </div>
                <div id="map"></div>
                <input type="hidden" id="lat" name="latitude">
                <input type="hidden" id="lng" name="longitude">
                <div id="coords-display" style="display:none;">
                    <i class="fas fa-map-pin"></i>
                    <span id="coords-text"></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Date de l'accident *</label>
                    <input type="date" name="date_accident" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description des dommages *</label>
                <textarea name="description" rows="5" required placeholder="Décrivez les circonstances et les dégâts..."></textarea>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Envoyer ma déclaration</button>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php?action=home" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo-area" style="margin-bottom: 0.8rem;">
                    <div class="logo-circle" style="background: white;"><span style="color:#6FAF4C;">GS</span></div>
                    <div class="logo-text"><h1 style="color:white;">GS Assurance</h1></div>
                </div>
                <p>Assurance auto nouvelle génération, alliée à des partenaires de confiance.</p>
            </div>
            <div class="footer-col">
                <h4>Liens utiles</h4>
                <p><a href="index.php?action=demande">Déclaration sinistre</a></p>
                <p><a href="index.php?action=historique">Historique & QR code</a></p>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p><i class="fas fa-phone-alt"></i> +216 70 123 456</p>
                <p><i class="fas fa-envelope"></i> contact@gsassurance.tn</p>
                <div class="social">
                    <i class="fab fa-facebook"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-linkedin"></i>
                </div>
            </div>
        </div>
        <div class="copyright">
            © 2025 GS Assurance – Transformation numérique & services durables
        </div>
    </div>
</footer>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var defaultLat = 36.8065, defaultLng = 10.1815; // Tunis
    var map = L.map('map').setView([defaultLat, defaultLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = null;

    function showCoords(lat, lng) {
        var el = document.getElementById('coords-display');
        var txt = document.getElementById('coords-text');
        el.style.display = 'flex';
        txt.textContent = 'Lat: ' + lat.toFixed(6) + '   Lng: ' + lng.toFixed(6);
    }

    function setMarker(lat, lng, address) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
        var popup = address ? address : 'Position sélectionnée';
        marker.bindPopup(popup).openPopup();
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        showCoords(lat, lng);
    }

    // Click on map to set location
    map.on('click', function(e){
        var lat = e.latlng.lat, lng = e.latlng.lng;
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, {
            headers: { 'Accept-Language': 'fr' }
        })
            .then(function(r){ return r.json(); })
            .then(function(data){
                var display = data.display_name || '';
                document.getElementById('lieu_accident').value = display;
                setMarker(lat, lng, display);
                hideAutocomplete();
            }).catch(function(){ setMarker(lat, lng, null); });
    });

    // Autocomplete
    var addrInput = document.getElementById('lieu_accident');
    var acList = document.getElementById('autocomplete-list');
    var searchTimeout = null;

    addrInput.addEventListener('input', function(){
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 3) { hideAutocomplete(); return; }
        searchTimeout = setTimeout(function(){
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&q=' + encodeURIComponent(q) + '&limit=6&accept-language=fr', {
                headers: { 'Accept-Language': 'fr' }
            })
                .then(function(r){ return r.json(); })
                .then(function(results){
                    showAutocomplete(results);
                }).catch(function(){ hideAutocomplete(); });
        }, 350);
    });

    function showAutocomplete(results) {
        if (!results || !results.length) { hideAutocomplete(); return; }
        acList.innerHTML = '';
        results.forEach(function(r){
            var item = document.createElement('div');
            item.className = 'autocomplete-item';
            var icon = r.type === 'road' ? 'fa-road' : (r.type === 'city' || r.type === 'town' ? 'fa-city' : 'fa-map-marker-alt');
            item.innerHTML = '<i class="fas ' + icon + '"></i><span>' + r.display_name + '</span>';
            item.addEventListener('mousedown', function(e){
                e.preventDefault();
                var lat = parseFloat(r.lat), lng = parseFloat(r.lon);
                addrInput.value = r.display_name;
                setMarker(lat, lng, r.display_name);
                map.setView([lat, lng], 15);
                hideAutocomplete();
            });
            acList.appendChild(item);
        });
        acList.style.display = 'block';
    }

    function hideAutocomplete() {
        acList.style.display = 'none';
    }

    addrInput.addEventListener('blur', function(){ setTimeout(hideAutocomplete, 200); });
    addrInput.addEventListener('keydown', function(e){ if(e.key === 'Escape') hideAutocomplete(); });

    // Geolocation button
    document.getElementById('btn-geolocate').addEventListener('click', function(){
        if (!navigator.geolocation) {
            alert('La géolocalisation n'est pas supportée par ce navigateur.');
            return;
        }
        var btn = this;
        btn.classList.add('loading');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';
        navigator.geolocation.getCurrentPosition(
            function(pos){
                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                map.setView([lat, lng], 16);
                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, {
                    headers: { 'Accept-Language': 'fr' }
                })
                    .then(function(r){ return r.json(); })
                    .then(function(data){
                        var display = data.display_name || 'Position GPS';
                        addrInput.value = display;
                        setMarker(lat, lng, display);
                        btn.classList.remove('loading');
                        btn.innerHTML = '<i class="fas fa-crosshairs"></i> Ma position';
                    }).catch(function(){
                        setMarker(lat, lng, 'Position GPS');
                        btn.classList.remove('loading');
                        btn.innerHTML = '<i class="fas fa-crosshairs"></i> Ma position';
                    });
            },
            function(err){
                alert('Impossible d'obtenir votre position. Veuillez activer la géolocalisation.');
                btn.classList.remove('loading');
                btn.innerHTML = '<i class="fas fa-crosshairs"></i> Ma position';
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    });

    // Restore existing values
    var existingLat = document.getElementById('lat').value;
    var existingLng = document.getElementById('lng').value;
    if (existingLat && existingLng) {
        setMarker(parseFloat(existingLat), parseFloat(existingLng), document.getElementById('lieu_accident').value || null);
        map.setView([parseFloat(existingLat), parseFloat(existingLng)], 14);
    }
});
</script>
</body>
</html>