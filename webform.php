<?php
// WebForm Processor.
//
// WebForm Processor is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// WebForm Processor is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License.
// If not, see <http://www.gnu.org/licenses/>.

/**
 *  Version information for WebForm Processor (v0.7)
 *
 *  @package    www.github.com/michael-milette/webform
 *  @author     Michael Milette | TNG Consulting Inc. - www.tngconsulting.ca
 *  @copyright  Copyright 2017-2019 TNG Consulting Inc.
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if(!isset($debug)) {
   $debug = false;
}
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $recipient = 'support@your.domain.tld';
}

// Default is English.

if (!empty($_GET['lang'])) {
    $page['lang'] = ($_GET['lang']  == 'fr' ? 'fr' : 'en');
} else {
    $page['lang'] = 'en';
}

// Language strings.
switch ($page['lang']) {
    case 'fr':
        $strings['error'] = 'Erreur';
        $strings['errormessage'] = 'Une ou plusieurs erreurs sont survenues&nbsp;:';
        $strings['invalidemail'] = "L'adresse courriel n'est pas valide.";
        $strings['invalidcemail'] = "L'adresses confirmation de courriel est différent.";
        $strings['missingfield'] = 'Vous devez completer tout les champs requis.';
        $strings['missingreferrer'] = 'Vous devez activer le référent pour utiliser ce formulaire.';
        $strings['spambotdetected'] = 'Spambot access denied.';
        $strings['emptyform'] = 'Vous devez remplir le formulaire.';
        $strings['tryagain'] = 'Revenir en arrière et réessayer de nouveau';
        $strings['thankyou'] = 'Merci';
        $strings['messagesent'] = "Le message a été envoyé.";
        $strings['messagefailed'] = "Le message n'as pas été livré.";
        break;
    default:
        $strings['error'] = 'Error';
        $strings['errormessage'] = 'One or more errors occurred:';
        $strings['invalidemail'] = 'Email address is invalid.';
        $strings['invalidcemail'] = 'The confirmation email address does not match.';
        $strings['missingfield'] = 'You must complete all of the required fields.';
        $strings['missingreferrer'] = 'You must enable referrer to use this form.';
        $strings['spambotdetected'] = 'Spambot access denied.';
        $strings['emptyform'] = 'You must complete the form.';
        $strings['tryagain'] = 'Go back and try again.';
        $strings['thankyou'] = 'Thank you';
        $strings['messagesent'] = "The message was successfully sent.";
        $strings['messagefailed'] = "The message was NOT sent.";
}

// Defaults form fields - You can override these by setting them in a parent contact.php file.

$subject = empty($subject) ? '' : $subject;
$htmlmessage = empty($htmlmessage) ? '' : $htmlmessage;
$sender = empty($sender) ? '' : $sender;

if (!isset($requiredFields)) {
    $requiredFields = [];
}

// Validation

// If SPAM, deny access.
$errors = isSPAM($requiredFields);

// Display any errors and exit if errors exist.

if (count($errors)) {
    $page['title'] = $strings['error'];
    $page['content'] = '<p>' . $strings['errormessage'] . '</p>';
    $page['content'] .= '<ul>' . PHP_EOL;
    foreach($errors as $value){
        $page['content'] .= "<li>$value</li>" . PHP_EOL;
    }
    $page['content'] .= '</ul>' . PHP_EOL;
    $page['content'] .= '<p>' . $strings['tryagain'] . '</p>' . PHP_EOL;
    require __DIR__ . '/page.php';
    exit;
}

// Process the submitted fields.

foreach ($_REQUEST as $key => $value) {
    // Only process key conforming to valid form field ID/Name token specifications.
    if (preg_match('/^[A-Za-z][A-Za-z0-9_:\.-]*/', $key)) {
        // Exclude fields we don't want in the message and empty fields.
        if (!in_array($key, array('submit', 'zip', 'cBee', 'cemail')) && trim($value) != '') {
            // Apply minor formatting of the key by replacing underscores with spaces.
            $key = str_replace('_', ' ', $key);
            switch ($key) {
                // Make custom alterations.
                case 'commentaires':  // Message field. Include in the message.
                case 'comments':      // Message field. Include in the message.
                case 'message':       // Message field. Include in the message.
                    // Strip out excessive empty lines.
                    $value = preg_replace('/\n(\s*\n){2,}/', "\n\n", $value);
                    // Sanitize the text.
                    $value = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    // Add to email message.
                    $htmlmessage .= '<p><strong>' . ucfirst($key) . ' :</strong></p><p>' . $value . '</p>';
                    break;
                    // Don't include in the body of the message.
                case 'objet':     // Email subject field. Don't include in body of message.
                case 'subject':   // Email subject field. Don't include in body of message.
                    $subject = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    break;
                case 'recipient': // Recipient field. Don't include in body of message.
                    if (empty($recipient)) {
                        $recipient = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    }
                    break;
                case 'lang':      // UI Language. Don't include in body of message.
                    $lang = ($value == 'fr' ? 'fr' : 'en');
                    break;
                case 'redirect':  // Redirect after successfully sent.
                    $redirect = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    break;
                case 'courriel':  // Email field. Don't include in body of message.
                case 'email':     // Email field. Don't include in body of message.
                    $sender = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    break;
                default:          // All other fields. Include in the message.
                    // Join array of values. Example: <select multiple>.
                    if (is_array($value)) {
                        $value = join($value, ", ");
                    }
                    // Sanitize the text.
                    $value = htmlentities (strip_tags(trim($value)), ENT_NOQUOTES);
                    if(!empty($value) || !empty($_REQUEST['print_blank_fields'])) {
                        // Add to email message.
                        $htmlmessage .= '<strong>'.ucfirst($key) . ' :</strong> ' . $value . '<br>' . PHP_EOL;
                    }
            }
        }
    }
}

$htmlmessage .= '<hr>' . PHP_EOL;
$htmlmessage .= '<strong>Additional information</strong><br><br>' . PHP_EOL;
$htmlmessage .= '<strong>IP address :</strong><br>' . getremoteaddr() . PHP_EOL;
$htmlmessage .= '<strong>User agent :</strong><br>' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;

// Send the message.

if (sendemail($sender, $recipient, $subject, $htmlmessage)) {
    if (!empty($redirect)) {
        header("Location: $redirect");
        exit;
    }
    $page['title'] = $strings['thankyou'];
    $page['content'] = '<p>' . $strings['messagesent'] . '</p>' . PHP_EOL;
} else {
    $page['title'] = $strings['error'];
    $page['content'] = '<p>' . $strings['messagefailed'] . '</p>' . PHP_EOL;
}
require __DIR__ . '/page.php';
exit;

// ==================== Supporting functions ===================== //

function optional_param($param, $default = '', $filter = FILTER_SANITIZE_STRING, $maxlen = 0) {
    if (isset($_REQUEST[$param])) {
        $value = $_REQUEST[$param];
        $value = filter_var(trim($value), $filter);
        $value = str_replace('../', '', $value);
    }
    if (!empty($maxlen) && !empty($value) && strlen($value) > $maxlen) {
        $value = $default;
    }
    if (empty($value)) {
        $value = $default;
    }
    return $value;
}

function isSPAM($requiredFields) {
    global $strings;
    
    // List of error messages.
    $errors = [];

    // Check for required fields.
    $chkemail = false;
    foreach($requiredFields as $field){
        if (empty($_REQUEST[$field])) {
            $errors[] = $strings['missingfield'];
            break;
        } else if ($field == 'email' || $field == 'courriel') {
            $chkemail = true;
        }
    }

    // Validate email field.
    $email = isset($_REQUEST['email']) && !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';
    if (empty($email)) {
        $email = isset($_REQUEST['courriel']) && !empty($_REQUEST['courriel']) ? $_REQUEST['courriel'] : '';
    }
    $cEmail = isset($_REQUEST['cemail']) && !empty($_REQUEST['cemail']) ? $_REQUEST['cemail'] : '';
    if ($chkemail && (
            empty($email)
            || $cEmail != $email
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || strlen($email) > 64)) {
        $errors[] = $strings['invalidemail'];
    }

    if ($cEmail != $email) {
        $errors[] = $strings['invalidcemail'];
    }

    // Check referrer is from same site.
    if (php_sapi_name() != "cli") {
        if(!(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST']))) {
            $errors[] = $strings['missingreferrer'];
        }
    }

    // Check for a blank form.
    $set = false;
    foreach($_REQUEST as $key => $value) {
        if (!empty($value) && in_array($key, ['submit', 'cBee', 'zip']) === false) {
            $set = true;
        }
    }
    if (! $set) {
        $errors[] = $strings['emptyform'];
    }

    // Check Honeypot.
    if(!empty($_REQUEST['zip']) || !empty($_REQUEST['cBee'])) {
        $errors[] = $strings['spambotdetected'];
    }

    return $errors;
}

function sendemail($from, $to, $subject, $htmlmessage) {
    global $debug, $_SERVER;

    $htmlmessage = "<html><body>$htmlmessage</body></html>\r\n";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "From: $to\r\n";
    $headers .= "Content-type: text/html;charset=UTF-8\r\n";

    if (!empty($from)) {
        $headers .= "Reply-To: $from\r\n";
    }

    $host = $_SERVER['HTTP_HOST'];
    return (mail($to, "[$host] " . $subject, $htmlmessage, $headers) !== false);
}

/**
 * Returns most reliable client address
 *
 * @param string $default If an address can't be determined, then return this
 * @return string The remote IP address
 */
function getremoteaddr($default='0.0.0.0') {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwardedaddresses = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
        $address = $forwardedaddresses[0];
        if (substr_count($address, ":") > 1) {
            // Remove port and brackets from IPv6.
            if (preg_match("/\[(.*)\]:/", $address, $matches)) {
                $address = $matches[1];
            }
        } else {
            // Remove port from IPv4.
            if (substr_count($address, ":") == 1) {
                $parts = explode(":", $address);
                $address = $parts[0];
            }
        }
         return $address;
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return $default;
    }
}
