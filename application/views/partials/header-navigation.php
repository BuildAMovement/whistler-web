<?php
/**
 * @var controller\base $this
 */
$request = $this->getRequest();
?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"> <img alt="Whistler" src="/static/img/logo@x2.png" width="140" height="40" alt="" class="img-responsive"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="<?php echo ($request->controller == 'page') && ($request->action == 'index') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'page', 'action' => 'index']); ?>">Home</a>
                </li>
                <li class="<?php echo ($request->controller == 'reports') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'reports']); ?>">Reports</a>
                </li>
                <li class="<?php echo ($request->controller == 'page')  && ($request->action == 'faq') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'page', 'action' => 'faq']); ?>">FAQ</a>
                </li>
                <li class="<?php echo ($request->controller == 'page')  && ($request->action == 'contact') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'page', 'action' => 'contact']); ?>">Contact us</a>
                </li>
                <li>
                    <a target="_blank" href="<?php echo $this->url(['controller' => 'download', 'action' => 'android']); ?>">Download</a>
                </li>
                <li class="support-us <?php echo ($request->controller == 'page')  && ($request->action == 'contact') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'page', 'action' => 'support-us']); ?>">Support us</a>
                </li>
                <!--<li class="<?php echo ($request->controller == 'page')  && ($request->action == 'download') ? 'active' : ''; ?>">
                    <a href="<?php echo $this->url(['controller' => 'page', 'action' => 'download']); ?>">Download</a>
                </li>-->
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>