<?php
session_start();
require_once "db.php";
?>

<!doctype html>
<html lang="de">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Restaurant Willy's Nudelhaus – Reservierung & Speisekarte</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --brand: #8a2b2b;
    }

    .navbar-brand span {
      color: var(--brand);
    }

    .hero {
      background: url("https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2c/d5/0d/31/serving-a-variety-of.jpg?w=900&h=500&s=1") center/cover no-repeat;
      min-height: 60vh;
      position: relative;
      color: #fff;
    }

    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
    }

    .hero>.container {
      position: relative;
      z-index: 1;
    }

    .section-title {
      border-left: 6px solid var(--brand);
      padding-left: 0.6rem;
      margin-bottom: 1.25rem;
    }

    .menu-card img {
      aspect-ratio: 4/3;
      object-fit: cover;
    }

    .badge-price {
      background: var(--brand);
      margin-left: 0.5rem;
    }

    .required::after {
      content: " *";
      color: var(--brand);
    }

    .page-section {
      padding: 90px 0;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <li class="nav-item">
            <a class="nav-link" href="#ueber-uns">Über uns</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="Speisekarte.php">Speisekarte</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#kontakt">Kontakt</a>
          </li>

          <li class="nav-item">
            <button class="btn btn-outline-dark ms-lg-2" data-bs-toggle="modal" data-bs-target="#reservierenModal">
              <i class="bi bi-calendar-check"></i> Reservieren
            </button>
          </li>

          <li class="nav-item dropdown ms-lg-2">
            <a class="btn btn-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person"></i> Konto
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">

              <?php if (!isset($_SESSION["user_id"])): ?>

                <li>
                  <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Einloggen
                  </a>
                </li>

                <li>
                  <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#registerModal">
                    Registrieren
                  </a>
                </li>

              <?php else: ?>

                <li class="dropdown-item disabled">
                  Eingeloggt (ID:
                  <?= $_SESSION["user_id"] ?>)
                </li>

                <li>
                  <hr class="dropdown-divider">
                </li>

                <li>
                  <a class="dropdown-item" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Mein Dashboard
                  </a>
                </li>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <h6 class="dropdown-header text-primary">Admin-Verwaltung</h6>
                  </li>
                  <li>
                    <a class="dropdown-item text-primary" href="admin_reservierungen.php">
                      <i class="bi bi-shield-lock"></i> Reservierungen
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item text-primary" href="admin_gerichte.php">
                      <i class="bi bi-egg-fried"></i> Speisekarte
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item text-primary" href="admin_users.php">
                      <i class="bi bi-people"></i> Benutzerkonten
                    </a>
                  </li>
                <?php endif; ?>

                <li>
                  <hr class="dropdown-divider">
                </li>

                <li>
                  <a class="dropdown-item text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </a>
                </li>

              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <?php if (isset($_GET['error'])): ?>
    <div class="container mt-3">
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Fehler:</strong>
        <?php
        switch ($_GET['error']) {
          case 'empty_fields':
            echo "Bitte füllen Sie alle Felder aus.";
            break;
          case 'invalid_login':
            echo "E-Mail oder Passwort ist nicht korrekt.";
            break;
          case 'no_login':
            echo "Bitte loggen Sie sich zuerst ein, um eine Reservierung vorzunehmen.";
            break;
          default:
            echo "Ein unerwarteter Fehler ist aufgetreten.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        Erfolgreich angemeldet! Willkommen zurück.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>

  <header class="hero d-flex align-items-center">
    <div class="container py-5 text-center text-lg-start">
      <div class="row justify-content-start">
        <div class="col-lg-6">
          <h1 class="display-4 fw-bold">Asiatische Küche & gemütliches Ambiente</h1>
          <p class="lead mt-3">
            Regionale Zutaten, saisonale Highlights und ein Service, der von Herzen kommt.
          </p>
          <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center justify-content-lg-start">
            <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#reservierenModal">
              <i class="bi bi-calendar-event"></i> Tisch reservieren
            </button>
            <a href="Speisekarte.php" class="btn btn-light btn-lg">
              <i class="bi bi-egg-fried"></i> Speisekarte ansehen
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <section id="ueber-uns" class="py-5">
    <div class="container">
      <h2 class="section-title h3">Über uns</h2>
      <div class="row g-4">
        <div class="col-lg-6">
          <p>
            Willkommen im <strong>Willys Nudelhaus</strong>. Bei uns treffen
            handwerkliche Küche, regionale Produkte und entspannte Atmosphäre aufeinander.
            Ob Dinner zu zweit, Familienfeier oder Geschäftsessen – wir freuen uns auf euch!
          </p>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <div class="p-3 border rounded-3 h-100 bg-white">
                <h6 class="mb-1 fw-bold text-uppercase small"><i class="bi bi-clock"></i> Öffnungszeiten</h6>
                <ul class="list-unstyled mb-0 small text-muted">
                  <li>Mo–Fr: 11:30–23:30</li>
                  <li>Sa, So & Feiertage: 11:30–23:30</li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 border rounded-3 h-100 bg-white">
                <h6 class="mb-1 fw-bold text-uppercase small"><i class="bi bi-geo-alt"></i> Adresse</h6>
                <p class="mb-0 small text-muted">
                  Kärntner Straße, 1010 Wien<br />
                  <a href="https://www.google.com/maps/search/?api=1&query=Kärntner+Straße+1010+Wien" target="_blank"
                    class="link-dark">
                    <i class="bi bi-map"></i> Auf Karte öffnen
                  </a>
                </p>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 offset-lg-1">
          <img class="w-100 rounded-4 shadow" src="images/Bild.png" alt="Restaurant Innenbereich">
        </div>
      </div>
    </div>
  </section>

  <section id="kontakt" class="py-5 bg-light">
    <div class="container">
      <h2 class="section-title h3">Kontakt</h2>
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm p-4">
            <form action="https://formspree.io/f/xzddekzb" method="POST" class="needs-validation" novalidate>
              <div class="mb-3">
                <label class="form-label required small fw-bold text-muted">Name</label>
                <input type="text" name="name" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label required small fw-bold text-muted">E-Mail</label>
                <input type="email" name="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label required small fw-bold text-muted">Nachricht</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
              </div>
              <button class="btn btn-dark w-100 py-2" type="submit">
                <i class="bi bi-send"></i> Nachricht senden
              </button>
            </form>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2981.1181071558667!2d16.36802297657783!3d48.2043853465075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x476d079c68169baf%3A0xd7d8b008c1ee805d!2sK%C3%A4rntner%20Str.%2C%201010%20Wien!5e1!3m2!1sde!2sat!4v1768748485773!5m2!1sde!2sat"
              width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
          <ul class="list-unstyled mt-3 small text-muted">
            <li class="mb-2"><i class="bi bi-telephone text-dark"></i> +43 690 12345678</li>
            <li><i class="bi bi-envelope text-dark"></i> willysnudel@gmail.com</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section id="reservierungen" class="py-5">
    <div class="container">
      <h2 class="section-title h3">Meine Reservierungen</h2>
      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>Datum</th>
                <th>Uhrzeit</th>
                <th>Personen</th>
                <th>Status</th>
                <th>Bemerkung</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!isset($_SESSION["user_id"])) {
                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Bitte loggen Sie sich ein, um Ihre Reservierungen zu sehen.</td></tr>";
              } else {
                $stmt = $pdo->prepare("SELECT datum, uhrzeit, personen, status, bemerkung FROM reservierungen WHERE user_id = ? ORDER BY datum DESC");
                $stmt->execute([$_SESSION["user_id"]]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Holen aller Reservierungen des Benutzers, Spaltenname werden zurückgegeben
              
                if (count($rows) > 0) {
                  foreach ($rows as $row) {
                    $statusClass = $row['status'] === 'bestätigt' ? 'bg-success' : ($row['status'] === 'storniert' ? 'bg-danger' : 'bg-secondary');
                    echo "<tr>
                          <td>" . date("d.m.Y", strtotime($row['datum'])) . "</td>
                          <td>" . substr($row['uhrzeit'], 0, 5) . " Uhr</td>
                          <td>{$row['personen']} Personen</td>
                          <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                          <td>" . (!empty($row['bemerkung']) ? htmlspecialchars($row['bemerkung']) : '-') . "</td>
                        </tr>";
                  }
                } else {
                  echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Noch keine Reservierungen vorhanden.</td></tr>";
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content border-0 shadow" action="login.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-right"></i> Einloggen</h5>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">E-Mail</label>
            <input type="email" name="email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Passwort</label>
            <input type="password" name="passwort" class="form-control" required />
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-dark w-100 py-2" type="submit">Jetzt Anmelden</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content border-0 shadow" action="register.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-person-plus"></i> Registrieren</h5>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Dein Name" required />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">E-Mail</label>
            <input type="email" name="email" class="form-control" placeholder="name@beispiel.de" required />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Passwort</label>
            <input type="password" name="passwort" class="form-control" required />
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-dark w-100 py-2" type="submit">Konto erstellen</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="reservierenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content border-0 shadow" action="reservieren.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-calendar-check"></i> Tisch reservieren</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">Datum</label>
            <input type="date" name="datum" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Uhrzeit</label>
            <input id="uhrzeitInput" type="time" name="uhrzeit" class="form-control" min="11:30" max="23:30" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Personen</label>
            <input id="personenInput" type="number" name="personen" class="form-control" min="1" max="11" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Bemerkung (optional)</label>
            <textarea name="bemerkung" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-dark w-100 py-2" type="submit">Reservierung senden</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Impressum & Datenschutz -->
  <section id="impressum" class="page-section py-5 bg-light">
    <div class="container">
      <h2 class="section-title h3">Impressum</h2>
      <p><strong>Willy's Nudelhaus</strong><br>
        Kärntner Straße<br>
        1010 Wien</p>
      <p><strong>Kontakt</strong><br>
        Telefon: +43 690 12345678<br>
        E‑Mail: willysnudel@gmail.com</p>
      <p><strong>Vertretungsberechtigt</strong><br>
        Willy Zhao und Tommy Ly</p>
      <p><strong>Umsatzsteuer-ID</strong><br>
        ATU12345678</p>
    </div>
  </section>

  <section id="datenschutz" class="page-section py-54">
    <div class="container">
      <h2 class="section-title h3">Datenschutz</h2>
      <p>Wir verarbeiten personenbezogene Daten nur im Rahmen der gesetzlichen Bestimmungen (DSGVO und DSG). Dazu zählen
        z. B. Daten aus Reservierungen, Kontaktformularen oder Benutzerkonten.</p>
      <ul>
        <li>Zweck: Bearbeitung von Reservierungen & Anfragen</li>
        <li>Rechtsgrundlage: Vertrag/Einwilligung</li>
        <li>Speicherdauer: nur so lange wie erforderlich</li>
        <li>Weitergabe: nur an Dienstleister, die für den Betrieb nötig sind</li>
      </ul>
      <p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung und Widerruf Ihrer Einwilligung.</p>
    </div>
  </section>

  <footer class="py-5 bg-dark text-light">
    <div class="container text-center">
      <p class="mb-0 small">© 2026 Willy's Nudelhaus - Alle Rechte vorbehalten.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>