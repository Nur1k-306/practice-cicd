<?php

session_start();

require_once __DIR__ . '/../app/functions.php';

$url = trim($_POST['long_url'] ?? '');

if (!is_valid_url($url)) {
    $_SESSION['msg'] = '❌ Введите корректный URL.';
    $_SESSION['msg_type'] = 'error';

    header('Location: /');
    exit;
}

$ttlRaw = trim($_POST['ttl'] ?? '');
$ttl = null;

if ($ttlRaw !== '') {
    if (!ctype_digit($ttlRaw) || (int) $ttlRaw < 60) {
        $_SESSION['msg'] = '❌ TTL должен быть целым числом больше или равно 60.';
        $_SESSION['msg_type'] = 'error';

        header('Location: /');
        exit;
    }

    $ttl = (int) $ttlRaw;
}

try {
    $code = store_url($url, $ttl);
    $linkData = fetch_link($code);
    $clicks = $linkData ? (int) $linkData['clicks'] : 0;

    $short = '/' . $code;

    $_SESSION['msg'] = sprintf(
        '✅ Ссылка создана: <a href="%1$s">%1$s</a> — кликов: %2$d',
        htmlspecialchars($short, ENT_QUOTES, 'UTF-8'),
        $clicks
    );
    $_SESSION['msg_type'] = 'msg';
} catch (Throwable $e) {
    $_SESSION['msg'] = '❌ Ошибка при сохранении ссылки: ' . $e->getMessage();
    $_SESSION['msg_type'] = 'error';
}

header('Location: /');
exit;