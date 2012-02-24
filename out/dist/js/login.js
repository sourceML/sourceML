function prepare_password(session_id)
{ document.forms["login_form"].pass.value = MD5(MD5(document.forms["login_form"].password.value) + session_id);
  document.forms['login_form'].password.value = "";
  return true;
}
