<?php
/**
 * AI Chat configuration.
 * Keep real API keys in config/ai_secret.php or environment variables.
 */

$aiSecretFile = __DIR__ . '/ai_secret.php';
$aiSecrets = is_file($aiSecretFile) ? require $aiSecretFile : [];

if (!function_exists('ai_config_value')) {
    function ai_config_value(array $secrets, string $key, string $default = ''): string
    {
        $env = getenv($key);
        if ($env !== false && $env !== '') {
            return $env;
        }

        return isset($secrets[$key]) && $secrets[$key] !== ''
            ? (string)$secrets[$key]
            : $default;
    }
}

define('AI_PROVIDER', ai_config_value($aiSecrets, 'AI_PROVIDER', 'openrouter'));

define('OPENROUTER_API_KEY', ai_config_value($aiSecrets, 'OPENROUTER_API_KEY'));
define('OPENROUTER_MODEL', ai_config_value($aiSecrets, 'OPENROUTER_MODEL', 'openai/gpt-oss-20b:free'));
define('OPENROUTER_SITE_URL', ai_config_value($aiSecrets, 'OPENROUTER_SITE_URL', 'http://localhost/QuanLyPhongtro'));
define('OPENROUTER_APP_NAME', ai_config_value($aiSecrets, 'OPENROUTER_APP_NAME', 'RoomManager'));

define('CLAUDE_API_KEY', ai_config_value($aiSecrets, 'CLAUDE_API_KEY'));
define('CLAUDE_MODEL', ai_config_value($aiSecrets, 'CLAUDE_MODEL', 'claude-haiku-4-5-20251001'));

define('GEMINI_API_KEY', ai_config_value($aiSecrets, 'GEMINI_API_KEY'));
define('GEMINI_MODEL', ai_config_value($aiSecrets, 'GEMINI_MODEL', 'gemini-2.0-flash'));

define('OPENAI_API_KEY', ai_config_value($aiSecrets, 'OPENAI_API_KEY'));
define('OPENAI_MODEL', ai_config_value($aiSecrets, 'OPENAI_MODEL', 'gpt-3.5-turbo'));

define('AI_SYSTEM_PROMPT', 'Ban la tro ly AI cua he thong quan ly phong tro RoomManager. Tra loi bang tieng Viet, ngan gon, than thien va dung du lieu he thong khi co.');
