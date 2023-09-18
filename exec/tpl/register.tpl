<script type='text/javascript'>
function request_username_()
{
  var username = document.getElementsByName('username')[0];
  url_username = '{%ajaxurl}&username=' + username.value;
}

function response_username_()
{
  var username = document.getElementsByName('username')[0];
  if(xml_result_username == 'duplicate') { alert('{%username_taken}: ' + username.value); }
}
</script>

{BBOXCENTER bboxsize::10}
{BPANEL|paneltitle::Register new user}

{BFORMSTART|user_register}
 {HIDDEN action registerdo}
 {HIDDEN|form_token|{%form_token}}

 {BTEXT username texttitle::Username onBlur::request_username();} 
 {BPASSWORD password Password size::4}
 {BPASSWORD|password1|Repeat password}
 {BTEXT email  texttitle::E-Mail}

 {BFORMSUBMIT class::center-block}
{BFORMEND}
{LINK|Login-page|user}
{/BPANEL}
{/BBOXCENTER}
