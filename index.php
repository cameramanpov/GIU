<?php
session_start();

// Gestion de l'authentification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = simplexml_load_file('credentials.xml');
    foreach ($users->user as $user) {
        if ($user->username == $username && $user->password == $password) {
            $_SESSION['username'] = $username;
            break;
        }
    }

    if (isset($_SESSION['username'])) {
        header('Location: index.php');
        exit;
    } else {
        echo '<script>alert("Nom d\'utilisateur ou mot de passe incorrect.");</script>';
    }
}

// Ajout d'un message
if (isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $author = $_SESSION['username'];

    $xml = simplexml_load_file('messages.xml');
    $newMessage = $xml->addChild('message');
    $newMessage->addChild('author', $author);
    $newMessage->addChild('content', $message);
    $xml->asXML('messages.xml');

    header('Location: index.php#messages');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groupe des informaticiens</title>
    <style>
        body {
            background-color: black;
            color: green;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .container {
            margin: 0 auto;
            max-width: 800px;
        }

        #toolbar {
            display: none;
        }

        #content img {
            width: 200px;
            margin-top: 20px;
        }

        .blue {
            color: blue;
        }

        .red {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (!isset($_SESSION['username'])) {
            echo '<div id="login-form">
                    <form method="post">
                        <input type="text" name="username" placeholder="Nom d\'utilisateur">
                        <input type="password" name="password" placeholder="Mot de passe">
                        <button type="submit">Connexion</button>
                    </form>
                </div>';
        } else {
            echo '<div id="main-content">
                    <div id="toolbar">
                        <button onclick="toggleToolbar()">Afficher/Cacher Toolbar</button>
                        <button onclick="loadPDFs()">Charger les PDFs</button>
                        <button onclick="openMessagePage()">Participer à la discussion</button>
                    </div>
                    <div id="content">
                        <h1>Groupe des informaticiens</h1>
                        <img src="logo.png" alt="Logo">
                    </div>
                </div>';
        }
        ?>

        <?php
        if (isset($_SESSION['username'])) {
            echo '<div id="messages">';
            $messages = simplexml_load_file('messages.xml');
            foreach ($messages->message as $message) {
                $author = $message->author;
                $content = $message->content;

                echo "<div class='message'>";
                echo "<span class='author'>" . $author . "</span>: ";
                echo "<span class='content'>" . $content . "</span>";
                if ($author == 'adminNj' || $author == $_SESSION['username']) {
                    echo "<button class='delete' onclick='deleteMessage(this)'>Supprimer</button>";
                }
                echo "</div>";
            }
            echo '</div>';

            echo '<form id="message-form" method="post">
                    <input type="text" name="message" placeholder="Votre message">
                    <button type="submit">Envoyer</button>
                </form>';
        }
        ?>
    </div>
    <script>
        function toggleToolbar() {
            var toolbar = document.getElementById("toolbar");
            toolbar.style.display = (toolbar.style.display == "none") ? "block" : "none";
        }

        function loadPDFs() {
            // Charger les PDF à partir du XML
        }

        function openMessagePage() {
            // Redirection vers la page de discussion
            window.location.href = "index.php#messages";
        }
    </script>
</body>
</html>
