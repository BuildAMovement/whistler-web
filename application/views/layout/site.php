<?php
/**
 * @var controller\base $this
 * @var string $content
 */
?>

<!DOCTYPE html>
<!--[if IE 9]><html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" class="ie ie9 "><![endif]-->
<!--[if IE 8]><html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" class="ie ie8 "><![endif]-->
<!--[if lte IE 7]><html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" class="ie ie7 "><![endif]-->
<!--[if !IE]><!-->
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml"
    class="">
<!--<![endif]-->
<head>
    <?php echo $this->partial('html-head.php'); ?>
</head>
<body class="<?php echo $this->getCurrentControllerName(), ' ', \ufw\registry::getInstance()->get('bodyCssClass'); ?>">
<?php echo $this->partial('header-navigation.php'); ?>
<?php echo $this->partial('page-header.php'); ?>
<?php echo $this->partial('flashmessages.php'); ?>

<div class="container">
    <?php echo $content; ?>
</div>
    
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.dotdotdot.min.js"></script>
<script type="text/javascript" src="/static/js/whistler.js"></script>
</body>
</html>