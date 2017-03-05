<?php

  require_once('../../private/initialize.php');

  if(isset($_POST['submit'])) {
    
    if(!isset($_GET['id'])) {
      redirect_to('index.php');
    }

    // get the recipient
    $id = urlencode($_GET['id']);
    $agent_result = find_agent_by_id($id);
    $recipient_agent = db_fetch_assoc($agent_result);

    // get the sender, is global from initialize
    $sender_agent = $current_user;

    // get the text
    $plaintext = u($_POST['plain_text']);

    // encrypt the message
    $encrypted_text = pkey_encrypt($plaintext, $recipient_agent[public_key]);

    // sign the message
    $signature = create_signature($encrypted_text, $sender_agent[private_key]);

    $message = [
      'sender_id' => $sender_agent['id'],
      'recipient_id' => $recipient_agent['id'],
      'cipher_text' => $encrypted_text,
      'signature' => $signature
  ];

    $result = insert_message($message);
    if($result === true) {
      // Just show the HTML below.
    } else {
      $errors = $result;
    }
    
  } else {
    redirect_to('index.php');
  }

?>

<!doctype html>

<html lang="en">
  <head>
    <title>Message Dropbox</title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <link rel="stylesheet" media="all" href="<?php echo DOC_ROOT . '/includes/styles.css'; ?>" />
  </head>
  <body>
    <a href="<?php echo url_for('/agents/index.php'); ?>">Back to List</a>
    <br/>

    <h1>Message Dropbox</h1>
    
    <div>      
      <p><strong>The message was successfully encrypted and saved.</strong></p>
        
      <div class="result">
        Message:<br />
        <?php echo h($encrypted_text); ?><br />
        <br />
        Signature:<br />
        <?php echo h($signature); ?>
      </div>
    </div>
    
  </body>
</html>
