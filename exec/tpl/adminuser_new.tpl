{BBOXCENTER}
{BPANEL|paneltitle::New Adminuser}
 
{BFORMSTART|admin_adminuser}
{HIDDEN|action|newdo}
{HIDDEN|form_token|{%form_token}}

  {BTEXT|username|texttitle::Username|val-required::1}
  {BPASSWORD|password|Password|val-required::1}
  {BPASSWORD|password1|Repeat Password|val-required::1|val-equalTo::password|val-err-equalTo::The Passwords must be equal}
  {BTEXTAREA|comment|10|Comment}

  {BFORMSUBMIT class::center-block}
{BFORMEND}

{/BPANEL}
{/BBOXCENTER}
