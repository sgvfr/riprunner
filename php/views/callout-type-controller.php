<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Protected Page</title>
        {% if gvm.isMobile %}
        <link rel="stylesheet" href="{{ gvm.RR_DOC_ROOT }}styles/mobile.css" />
        {% else %}
        <link rel="stylesheet" href="{{ gvm.RR_DOC_ROOT }}styles/main.css" />
        {% endif %}
        <script type="text/JavaScript" src="{{ gvm.RR_DOC_ROOT }}js/jquery-2.1.1.min.js"></script>
    </head>
    <body>
        <div class="container_center">
        {% if gvm.auth.isAuth %}
            {% include 'user-welcome.twig.html' %}
            {% include 'live-callout-warning.twig.html' %}

            {% if gvm.auth.isAdmin and mainmenu_vm.hasApplicationUpdates %}
                <br />
                <span class='notice'>Current Version {{ mainmenu_vm.LOCAL_VERSION }} 
                New Version {{ mainmenu_vm.REMOTE_VERSION }}</span>
                <br />
                <a target='_blank' href='{{ mainmenu_vm.REMOTE_VERSION_NOTES }}' 
                   class='notice'>Click here for update information</a>
            {% endif %}
            
            <div class="menudiv_wrapper">
              <nav class="vertical">
                <ul>
                  {% if gvm.auth.isAdmin %}
                  <li>
                    <label for="admin">Admin</label>
                    <input type="radio" name="verticalMenu" id="admin" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/users-menu-controller.php">User Accounts</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/callout-type-controller.php">Callout Codes</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/callout-status-controller.php">Response Codes</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/system-config-controller.php">System Settings</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/system-info-controller.php">System Information</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/system-test-controller.php">System Testing</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/view-logs-controller.php">System Logs</a></li>
                      </ul>
                    </div>
                  </li>
                  {% endif %}
                  <li>
                    <label for="call_history">Calls</label>
                    <input type="radio" name="verticalMenu" id="call_history" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/callout-history-controller.php">Callouts and Responders</a></li>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/callout-monitor-controller.php?server_mode=false">Monitor Live Callouts</a></li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label for="reports">Reports</label>
                    <input type="radio" name="verticalMenu" id="reports" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/reports-charts-controller.php">Charts</a></li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label for="mobile_app">Mobile</label>
                    <input type="radio" name="verticalMenu" id="mobile_app" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}apk/RipRunnerApp.apk">Install Android App</a></li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label for="my_account">My Profile</label>
                    <input type="radio" name="verticalMenu" id="my_account" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}controllers/users-menu-controller.php?se=true">My Account</a></li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label for="logout">Exit</label>
                    <input type="radio" name="verticalMenu" id="logout" />
                    <div>
                      <ul>
                        <li><a href="{{ gvm.RR_DOC_ROOT }}logout.php">Logout</a></li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </nav>
            </div>
        {% else %}
            {% include 'access-denied.twig.html' %}
        {% endif %}
        </div>
    </body>
</html>