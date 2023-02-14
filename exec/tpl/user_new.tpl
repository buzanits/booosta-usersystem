{BBOXCENTER}
{BPANEL|paneltitle::New User}

{BFORMSTART|admin_user{%script_extension}}
{HIDDEN|action|newdo}
{HIDDEN|customer|{%customer}}
{HIDDEN|form_token|{%form_token}}

  {BTEXT|username|texttitle::Username|val-required::1}
  {%sel_region}
  {BPASSWORD|password|Password|val-required::1}
  {BPASSWORD|password1|Repeat Password|val-required::1|val-equalTo::password|val-err-equalTo::Die Passwords must be equal}
  {BTEXTAREA|comment|10|Comment}

  {BFORMSUBMIT|class::center-block}
{BFORMEND}

{/BPANEL}
{/BBOXCENTER}
