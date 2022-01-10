<p class="login-box-msg">Sign in to start your session</p>


<div class="alert alert-danger" role="alert">
%if({%login_message})
  <center>{%login_message}</center>
%else
  <center>You are not logged in or your session has expired.</center>
</div>

{FORMSTART|login_user_do|class::form-signin|role::form}
        <div class="input-group mb-3">
          {TEXT username {%username} 12 class::form-control placeholder::Username required::1 autofocus::1}
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          {PASSWORD password {%password} 12 class::form-control placeholder::Password required::1}
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
%if({%login_token}):
        <div class="input-group mb-3">
          {TEXT token {%token} 12 class::form-control placeholder::Token required::1}
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
%elseif({%login_token_optional}):
        <div class="input-group mb-3">
        {TEXT token {%token} 12 class::form-control placeholder::Token}
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
%endif;        
        <div class="row">
          <div class="col-9">
            <div class="icheck-primary">
              {CHECKBOX|rememberme|id::remember}
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-3">
            {BFORMSUBMIT buttontext::Login}
          </div>
          <!-- /.col -->
        </div>
{FORMEND}

<p class="mb-1">
  {LINK|Password forgotten|user_register/resetpassword}
</p>
%if({%registration_allowed}):
<p class="mb-0">
  {LINK|Register user|user_register}<br>
</p>
%endif;
