<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";
 
    $ai_result  = null;
    $form_error = null;

    $CntrlOffre=new controlOffre();
    $offre=$CntrlOffre->listeOffre('');

    $CntrlType=new ControlTypes();
    $type=$CntrlType->listeType('');

    $ControlInscri=new Controlnscription();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ai_approved'])) {

        $payment_status ='Paid';
        $inscri=new Inscription(
            new DateTime($_POST['date_souscription']),
            new DateTime($_POST['date_expiration']),
            $payment_status,
            $_POST['Payment_method'],
            (int)$_POST['Montant_paye'],
            new DateTime(),
            $_POST['Title'] ?? ''
        );
        if(empty($_POST['InscriptionID'])){
            $ok=$ControlInscri->addInscription($inscri);
            if($ok){
                header('Location: InscriptionPage.php?action=add&OffreID=' . urlencode($_POST['Title']) . '&success=accepted');
                exit();
            }
        }
    }
    
    $action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Inscription</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <script src="./assets/js/inscri.js" defer></script>
    </head>
    
    <body>
        <div id="ai-overlay">
            <div class="ai-modal">

                <div id="step-loading">
                    <div class="ai-spinner"></div>
                    <div class="ai-loading-title">Analyse IA en cours...</div>
                    <div class="ai-loading-sub" id="loading-msg">Vérification des données...</div>
                </div>
                
                <div id="step-result">
                    <div class="ai-verdict-icon"  id="result-icon"></div>
                    <div class="ai-verdict-title" id="result-title"></div>
                    <div class="ai-verdict-summary" id="result-summary"></div>
                    <div class="ai-score-bar">
                        <div class="ai-score-fill" id="score-fill"></div>
                    </div>
                    <div class="ai-raison"  id="result-raison"  style="display:none;"></div>
                    <div class="ai-checks"  id="result-checks"></div>
                    <button class="ai-btn ai-btn-confirm" id="btn-confirm"
                            style="display:none;" onclick="confirmerInscription()">
                        ✓ Confirmer ma souscription
                    </button>
                    <button class="ai-btn ai-btn-retry" id="btn-retry"
                            onclick="fermerOverlay()">
                        ← Corriger le formulaire
                    </button>
                </div>
 
            </div>
        </div>
        <div class="animated-bg"></div>

        <div class="floating-shape shape1"></div>
        <div class="floating-shape shape2"></div>
        <div class="floating-shape shape3"></div>
        <div class="floating-shape shape4"></div>
        <div class="floating-shape shape5"></div>
        <div class="bord">
            <div class="bord-left">
                <div class="logo_area">
                    <img src="../images/logo.png" alt="logo" height="50" width="65">
                </div>
                <div class="slogon">
                    <h1>GreenSecure</h1>
                    <small>Assurance verte, avenir serein</small>
                </div>
            </div>
            <div class="bord-right">
                <a href="accueil.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="#assurances"><i class="fas fa-shield-alt"></i> Nos assurances</a>
                <!-- NOUVEAU BOUTON GREENBOT - CHATBOT -->
                <a href="chatbot.php" class="btn-greenbot"><i class="fas fa-robot"></i> 💬 GreenBot</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../backoffice/liste.php"><i class="fas fa-users"></i> Administration</a>
                    <a href="profil.php?id=<?= $_SESSION['user_id'] ?>"><i class="fas fa-user"></i> Mon Profil</a>
                    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn-nav-primary"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="../auth/register.php"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="offre">
            <?php if($action=='list'){?>
                <h1>Choisir un Offre</h1>
                <?php $selectedType = $_GET['TypeID'] ?? null;
                if(!empty($offre)){
                    foreach($offre as $o){ 
                        if($o['Status']!=='inactive' && ($selectedType === null || $o['Type'] == $selectedType)){?>
                            <a class="card" href="InscriptionPage.php?action=add&OffreID=<?= urlencode($o['Title']) ?>&TypeID=<?= urlencode($selectedType ?? '') ?>">
                                <div class="card-header"><h2><?= htmlspecialchars($o['Title']) ?></h2></div>
                                <div class="card-body">
                                    <div class="line">
                                        <span class="label">Prix</span>
                                        <span class="value"><?= htmlspecialchars($o['Prix_mensuel']) ?></span>
                                    </div>

                                    <div class="line">
                                        <span class="label">Début</span>
                                        <span class="value"><?= htmlspecialchars($o['Date_Debut']) ?></span>
                                    </div>

                                    <div class="line">
                                        <span class="label">Fin</span>
                                        <span class="value"><?= htmlspecialchars($o['Date_Fin']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php }?>
                <?php } else{?>
                    <div class="card"><p>Aucun offre d'assurance disponible pour le moment.</p></div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="inscri" id="formulaire">
            <?php if($action == 'add'){ ?>
                <div class="card" style="align-items: center;">
                    <h1 style="text-align: center; color:white;">Remplir ce formulaire</h1>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="success-banner">
                            <div class="sb-header">✓ Demande acceptée et enregistrée</div>
                            <div class="sb-body">Votre souscription a été validée par notre système IA. Bienvenue chez GreenSecure !</div>
                        </div>
                    <?php endif; ?>
 
                    <?php if (!isset($_GET['success'])): ?>
                    <form id="inscription-form" method="post" class="form" onsubmit="return lancerAnalyse(event)">
                        <input type="hidden" name="InscriptionID" value="<?= $_GET['InscriptionID'] ?? '' ?>">
                        <input type="hidden" name="Title" value="<?= htmlspecialchars($_GET['OffreID'] ?? '') ?>">
                        <input type="hidden" name="ai_approved"   value="1">
                        <select id="Payment_method" name="Payment_method">
                            <option value="">--Veuillez choisir la methode de payment--</option>
                            <option value="Carte">Carte</option>
                            <option>Virement</option>
                            <option>Cheque</option>
                            <option>Especes</option>
                        </select>
                        <label>Donner la date de souscription:</label>
                        <input type="date" name="date_souscription">
                        <label>Donner la date d'expiration:</label>
                        <input type="date" name="date_expiration">
                        <label>Montant Paye</label>
                        <input type="number" id="Montant_paye" name="Montant_paye" min=0>
                        <div id="variables-container"></div>
                        <button type="submit" value="Add abonne">Submit</button>
                        <p id="form-message"></p>
                        <script>
                            setTimeout(() => {
                                const msg = document.querySelector(".success")
                                if(msg) msg.style.display = "none"
                            }, 13000)
                        </script>
                        <?php if(isset($_GET['success'])): ?>
                            <script>
                                alert("Formulaire vaide ✔");
                            </script>
                        <?php endif; ?>
                    </form>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
        <div class="contacts" id="contacts">
            <p>📍 Tunis, Tunisie &nbsp;</p>
            <p>&nbsp;📞 +216 70 123 456 &nbsp;|&nbsp; ✉️ contact@greensecure.tn</p>
        </div>
        <script>
            const colors = ["#6FAF4C", "#A67C52", "#b11e1e", "#1c64ca", "#FFC300"];

            document.querySelectorAll(".offre .card").forEach(card => {
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                card.style.setProperty("--card-color", randomColor);
            })

            const loadingMsgs = [
                'Vérification des données...',
                'Contrôle du numéro de carte...',
                'Validation du code postal...',
                'Calcul du score de confiance...',
                'Finalisation de l\'analyse...'
            ];
            let loadingInterval = null;
 
            function demarrerMessages() {
                let i = 0;
                const el = document.getElementById('loading-msg');
                el.textContent = loadingMsgs[0];
                loadingInterval = setInterval(() => {
                    i = (i + 1) % loadingMsgs.length;
                    el.textContent = loadingMsgs[i];
                }, 1200);
            }
            function arreterMessages() { clearInterval(loadingInterval); }

            async function lancerAnalyse(e) {
                e.preventDefault();

                const clientOk = validerFormulaire();
                if (!clientOk) return false;

                const form = document.getElementById('inscription-form');
                const data = {};
                new FormData(form).forEach((v, k) => data[k] = v);

                document.getElementById('step-loading').style.display = 'block';
                document.getElementById('step-result').style.display  = 'none';
                document.getElementById('ai-overlay').classList.add('active');
                demarrerMessages();

                let result;
                try {
                    const resp = await fetch('./ai_check.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    result = await resp.json();
                } catch (err) {
                    result = {
                        verdict: 'REFUSÉ', score: 0,
                        résumé: 'Erreur de connexion au service IA.',
                        raison_refus: err.message, checks: []
                    };
                }
 
                arreterMessages();

                afficherResultat(result);
            }
 
            function afficherResultat(r) {
                const accepted = r.verdict === 'ACCEPTÉ';
 
                document.getElementById('step-loading').style.display = 'none';
                document.getElementById('step-result').style.display  = 'block';
 
                document.getElementById('result-icon').textContent = accepted ? '✓' : '✕';
 
                const titleEl = document.getElementById('result-title');
                titleEl.textContent = accepted ? 'Demande acceptée' : 'Demande refusée';
                titleEl.className   = 'ai-verdict-title ' + (accepted ? 'ok' : 'ko');
 
                document.getElementById('result-summary').textContent = r.résumé || '';
 
                const score = r.score ?? (accepted ? 90 : 20);
                const fill  = document.getElementById('score-fill');
                fill.style.width  = score + '%';
                fill.className    = 'ai-score-fill ' + (accepted ? 'score-ok' : 'score-ko');
 
                const raisonEl = document.getElementById('result-raison');
                if (!accepted && r.raison_refus) {
                    raisonEl.textContent   = '⚑ ' + r.raison_refus;
                    raisonEl.style.display = 'block';
                } else {
                    raisonEl.style.display = 'none';
                }
 
                const checksEl = document.getElementById('result-checks');
                checksEl.innerHTML = '';
                (r.checks || []).forEach(c => {
                    const icon = c.statut === 'ok' ? '✓' : '✕';
                    const cls  = c.statut === 'ok' ? 'ok' : 'fail';
                    checksEl.innerHTML += `
                        <div class="ai-check-row ${cls}">
                            <span>${icon}</span>
                            <span class="ck-field">${c.champ}</span>
                            <span class="ck-msg">${c.message}</span>
                        </div>`;
                });
 
                const btnConfirm = document.getElementById('btn-confirm');
                const btnRetry   = document.getElementById('btn-retry');
                btnConfirm.style.display = accepted ? 'block' : 'none';
                btnRetry.textContent     = accepted ? '← Annuler' : '← Corriger le formulaire';
            }
            function confirmerInscription() {
                document.getElementById('inscription-form').submit();
            }
 
            function fermerOverlay() {
                document.getElementById('ai-overlay').classList.remove('active');
            }
        </script>
    </body>
</html>