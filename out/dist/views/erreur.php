<div class="erreur">
<strong>Erreur</strong>
<?php if(isset($this->out["erreur"]["messages"]) && $this->out["erreur"]["messages"]) : ?>
  <ul>
<?php foreach($this->out["erreur"]["messages"] as $message) : ?>
    <li><?php echo $message ?></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>