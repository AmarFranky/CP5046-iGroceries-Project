<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="robots" content="noindex, nofollow">
<?php if ( isset( $CONF["BASE_URL"] ) && isset( $CONF_EXTEND ) && is_file( "$CONF[CONF_ROOT]/favicon.ico" ) ): ?>
<link rel="shortcut icon" href="<?php echo $CONF["BASE_URL"] ?>/web/<?php echo $CONF_EXTEND ?>/favicon.ico">
<?php else: ?>
<link rel="shortcut icon" href="<?php echo $CONF["BASE_URL"] ?>/favicon.ico">
<?php endif ; ?>
