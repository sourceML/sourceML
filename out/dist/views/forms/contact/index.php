<div id="contact" class="description">

  <h2>Contact</h2>

  <form action="<?= $this->url("forms/contact") ?>" method="post">
    <ul class="form">
      <li>
        <label for="email">email</label>
        <p>
          <input type="text" name="email" id="email" value="<?= $_POST["email"] ?>" />
        </p>
      </li>
      <li>
        <label for="message">message</label>
        <p>
          <textarea name="message" id="message" rows="16" cols="50"><?= $_POST["message"] ?></textarea>
        </p>
      </li>
<?php if($this->config("captcha")) : ?>
      <li>
        <label for="ptitcaptcha_entry">anti-spam</label>
        <p>
          <?= PtitCaptchaHelper::generateImgTags($this->path("libs")) ?>
          <?= PtitCaptchaHelper::generateHiddenTags() ?>
          <?= PtitCaptchaHelper::generateInputTags() ?>
        </p>
      </li>
<?php endif; ?>
      <li class="buttons">
        <input type="submit" value="Envoyer" />
      </li>
    </ul>
  </form>

</div>
