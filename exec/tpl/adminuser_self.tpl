{BBOXCENTER}
{BPANEL|paneltitle::Change Password}

{BFORMSTART|admin_self{%script_extension}|onSubmit::return checkForm();}
 {HIDDEN|action|editdo}
 {HIDDEN|id|{%id}}
 {HIDDEN|form_token|{%form_token}}

 
 {BSTATIC|{*username}|Username}
 {BPASSWORD|curpassword|Current Password}
 {BPASSWORD|password|New Password}
 {BPASSWORD|password1|Repeat new password}

 {BFORMSUBMIT|class::center-block}
{BFORMEND}
{/BPANEL}
{/BBOXCENTER}

%if({%show_token}):
{BBOXCENTER}
{BPANEL|paneltitle::Two factor Login}

{%token_links}

{/BPANEL}
{/BBOXCENTER}
%endif;
