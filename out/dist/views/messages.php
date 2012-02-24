<?php if(isset($this->out["messages"]) && $this->out["messages"]) : ?>
<div class="messages">
  <ul>
<?php foreach($this->out["messages"] as $message) : ?>
    <li><?= $message ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>