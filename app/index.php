<?php
function loadEnv($file = '.env') {
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            putenv("$name=$value");
        }
    }
}

loadEnv();
$action_url = getenv('FORM_ACTION_URL');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>PHP Form</title>
</head>
<body>
  <?php
  function render_form($errors, $values)
  {
      ?>
    <form class="comment-form" method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">
    <div class="steps">
        <ul>
          <li class="current">1</li>
          <li>2</li>
          <li>3</li>
        </ul>
      </div>
        <p class="comment-form__title">メッセージを送る</p>
        <div class="comment-form__row">
        <label for="name" class="comment-form__label">名前 *</label>
        <input type="text" class="comment-form__input" name="name" id="name" required value="<?php echo $values[
            "name"
        ]; ?>" />
        <?php foreach ($errors["name"] as $error) { ?>
          <p class="comment-form__error"><?php echo $error; ?></p>
        <?php } ?>
        </div>
        <div class="comment-form__row">
        <label for="mail" class="comment-form__label">メールアドレス</label>
        <input type="text" class="comment-form__input" name="mail" id="mail" value="<?php echo $values[
            "mail"
        ]; ?>" />
        <?php foreach ($errors["mail"] as $error) { ?>
          <p class="comment-form__error"><?php echo $error; ?></p>
        <?php } ?>
        </div>
        <div class="comment-form__row">
        <label for="contact" class="comment-form__label">お問い合わせ内容 *</label>
        <textarea class="comment-form__textarea" id="contact" name="contact" required minlength="10" maxlength="100"><?php echo $values[
            "contact"
        ]; ?></textarea>
        <?php foreach ($errors["contact"] as $error) { ?>
          <p class="comment-form__error"><?php echo $error; ?></p>
        <?php } ?>
        </div>
        <div class="comment-form__submit-row">
        <input type="submit" value="確認画面へ" class="comment-form__submit" aria-label="確認画面へ"/>
        </div>
    </form>
  <?php
  }
  function validate_form()
  {
      $errors = [
          "name" => [],
          "mail" => [],
          "contact" => [],
      ];
      if ($_SERVER["REQUEST_METHOD"] !== "POST") {
          return $errors;
      }
      $name = trim($_POST["name"]);
      if ($name === "") {
          $errors["name"][] = "名前を入力してください";
      }
      $mail = trim($_POST["mail"]);
      if ($mail === "") {
          $errors["mail"][] = "メールアドレスを入力してください";
      } else if (!filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {
          $errors["mail"][] = "メールアドレスの形式が正しくありません";
      }
      $contact = trim($_POST["contact"]);
      if ($contact === "") {
          $errors["contact"][] = "お問い合わせ内容を入力してください";
      } else if (mb_strlen($_POST["contact"]) < 10) {
          $errors["contact"][] =
              "お問い合わせ内容は10文字以上で入力してください";
      }
      return $errors;
  }
  function get_default_values()
  {
      $values = [
          "name" => "",
          "mail" => "",
          "contact" => "",
      ];
      if ($_SERVER["REQUEST_METHOD"] !== "POST") {
          return $values;
      }
      $values["name"] = htmlspecialchars($_POST["name"]);
      $values["mail"] = htmlspecialchars($_POST["mail"]);
      $values["contact"] = htmlspecialchars($_POST["contact"]);
      return $values;
  }
  function has_error($errors)
  {
      foreach ($errors as $error) {
          if (count($error) > 0) {
              return true;
          }
      }
      return false;
  }

  $errors = validate_form();
  $values = get_default_values();
  ?>
  <?php if ($_SERVER["REQUEST_METHOD"] === "GET"): ?>
    <?php render_form($errors, $values); ?>
  <?php elseif (has_error($errors)): ?>
    <?php render_form($errors, $values); ?>
  <?php else: ?>
    <div class="comment-form">
    <div class="steps">
        <ul>
          <li>1</li>
          <li class="current">2</li>
          <li>3</li>
        </ul>
      </div>
      <p class="comment-form__title">確認画面</p>
      
      <table>
        <tr>
          <th>名前</th>
          <td><?php echo htmlspecialchars($_POST["name"]); ?></td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td><?php echo htmlspecialchars($_POST["mail"]); ?></td>
        </tr>
        <tr>
          <th>お問い合わせ内容</th>
          <td><?php echo htmlspecialchars($_POST["contact"]); ?></td>
        </tr>
      </table>

      <form action="<?php echo htmlspecialchars($action_url); ?>" method="post">
        
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($_POST["name"]); ?>">
            <input type="hidden" name="mail" value="<?php echo htmlspecialchars($_POST["mail"]); ?>">
            <input type="hidden" name="contact" value="<?php echo htmlspecialchars($_POST["contact"]); ?>">

            <div class="comment-form__submit-row">
            <button type="button" onclick="history.back()" class="comment-form__submit-back">修正</button>
            <button type="submit" class="comment-form__submit">送信</button>
            
            </div>
        </form>
    </div>
  <?php endif; ?>
</body>

</html>