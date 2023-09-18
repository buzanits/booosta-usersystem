{BBOXCENTER|bboxsize::10}
{BPANEL|paneltitle::Set new password}

{BFORMSTART|user_register}
{HIDDEN|action|setpassword}
{HIDDEN|username|{%username}}
{HIDDEN|token|{%token}}

{BBOXROWSTART}
{BBOXSTART|6}
 {BPASSWORD|password|New Password|size::4}
 {BPASSWORD|password1|Repeat Password}

{BBOXEND}
{BBOXROWEND}

 {BFORMSUBMIT|class::center-block}
{BFORMEND}
{/BPANEL}
{/BBOXCENTER}
