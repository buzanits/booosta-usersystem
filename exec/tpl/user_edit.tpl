{BBOXCENTER}
{BPANEL|paneltitle::Edit User}

{BFORMSTART|admin_user{%script_extension}}
{HIDDEN|action|editdo}
{HIDDEN|id|{%object_id}}
{HIDDEN|form_token|{%form_token}}

  {BCHECKBOX|active|{%active}|texttitle::Active}
  {BTEXT|username|{%username}|texttitle::Username|val-required::1}
  {%sel_region}
  {BPASSWORD|password|Password (leave empty for no change)}
  {BPASSWORD|password1|Repeat Password|val-equalTo::password|val-err-equalTo::The passwords must be equal}
  {BTEXTAREA|comment|10|Comment
{%comment}}
  {BFORMGRP|Privileges|size::4}{%sel_privileges}{/BFORMGRP}
  {BFORMGRP|Roles|size::4}{%sel_roles}{/BFORMGRP}

  {BFORMSUBMIT|class::center-block}
  
  {BLINK|Force password change|admin_user{%script_extension}{%script_actionstr}forcepwchange{%script_divider}user_id={%object_id}}
{BFORMEND}

{/BPANEL}
{/BBOXCENTER}

<br><br>

%if({%show_token}):
{BBOXCENTER}
{BPANEL|paneltitle::Twofactor-Login}

{%token_links}

{/BPANEL}
{/BBOXCENTER}
%endif;

