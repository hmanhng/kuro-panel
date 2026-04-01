<!doctype html>
<html lang="en">

<head>
    <script>
		window.onload=function(){
			document.getElementById('loader').style.display="none";
			document.getElementById('content').style.display="block";
		};
		</script>
		
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    

    <title><?= BASE_NAME ?> - <?= isset($title) ? $title : 'Panel' ?></title>
    <?= $this->renderSection('css') ?>

    <?= link_tag('assets/css/natacode.css') ?>
    <?= link_tag('assets/css/sb-admin-2.min.css') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <style>
		:root{
			--retro-bg:#f3eadf;
			--retro-bg-soft:#efe3d3;
			--retro-paper:#fff9f1;
			--retro-ink:#1d1f24;
			--retro-muted:#6f665d;
			--retro-accent:#d86729;
			--retro-line:#1f232b;
			--retro-shadow:6px 6px 0 rgba(20, 22, 29, 0.95);
			--retro-shadow-soft:4px 4px 0 rgba(20, 22, 29, 0.65);
		}
		body{
			font-family:"Space Grotesk",sans-serif;
			color:var(--retro-ink);
			background:
				radial-gradient(circle at 14% 18%, rgba(216,103,41,.2) 0 9%, transparent 9.1%),
				radial-gradient(circle at 84% 10%, rgba(31,111,139,.17) 0 12%, transparent 12.1%),
				repeating-linear-gradient(-45deg, rgba(29,31,36,.025) 0 9px, transparent 9px 18px),
				linear-gradient(180deg, var(--retro-bg) 0%, var(--retro-bg-soft) 100%);
			min-height:100vh;
		}
		#content{
			display:none;
			animation:retroContentIn .45s ease-out both;
		}
		#loader{
			position: absolute;
			margin: auto;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			width: 400px;
			height: 400px;
		}
		#loader img{width:400px;}
		.navbar{
			border:2px solid var(--retro-line)!important;
			border-radius:14px;
			background:linear-gradient(180deg,#fffdf8,#f5e8d8)!important;
			box-shadow:var(--retro-shadow-soft)!important;
			margin:12px 12px 0;
		}
		.navbar .navbar-brand,
		.navbar .nav-link{font-weight:700;letter-spacing:.01em;}
		.navbar .nav-link{
			border-radius:10px;
			transition:background-color .18s ease,color .18s ease,transform .18s ease;
		}
		.navbar .nav-link:hover{
			background:#fff1df;
			color:var(--retro-accent)!important;
			transform:translateY(-1px);
		}
		.dropdown-menu{
			border:2px solid var(--retro-line)!important;
			border-radius:12px!important;
			background:#fffaf3!important;
			box-shadow:var(--retro-shadow-soft)!important;
			transform-origin:top right;
			animation:retroDropdownIn .18s ease-out;
		}
		.dropdown-item{font-weight:600;border-radius:8px;}
		.dropdown-item:hover{background:#ffe6cf!important;}
		.card,
		.modal-content{
			border:2px solid var(--retro-line)!important;
			border-radius:16px!important;
			background:var(--retro-paper)!important;
			box-shadow:var(--retro-shadow)!important;
		}
		.card.bg-primary,
		.card.bg-gradient-light{
			background:var(--retro-paper)!important;
		}
		.modal-header.bg-primary{
			background:linear-gradient(180deg,#fff8ed,#f8ecdd)!important;
			color:var(--retro-ink)!important;
		}
		.card-header{
			border-bottom:2px dashed rgba(31,35,43,.35)!important;
			background:linear-gradient(180deg,#fff8ed,#f8ecdd)!important;
		}
		.modal-content{
			animation:retroModalIn .2s ease-out;
		}
		.modal-header{
			border-bottom:2px dashed rgba(31,35,43,.35)!important;
			background:linear-gradient(180deg,#fff8ed,#f8ecdd)!important;
		}
		.modal-footer{
			border-top:2px dashed rgba(31,35,43,.25)!important;
			background:#fff7ec!important;
		}
		.table{
			--bs-table-bg:transparent!important;
		}
		.table thead th{
			font-family:"IBM Plex Mono",monospace;
			font-size:.72rem!important;
			letter-spacing:.06em;
			color:#4e4339!important;
		}
		.table td,.table th{
			border-color:rgba(29,31,36,.14)!important;
		}
		.table tbody tr{
			transition:transform .13s ease, background-color .13s ease;
		}
		.table-hover tbody tr:hover{
			background:rgba(216,103,41,.08)!important;
			transform:translateX(2px);
		}
		.badge{
			border:1.5px solid rgba(29,31,36,.42);
			box-shadow:2px 2px 0 rgba(29,31,36,.35);
		}
		.btn{
			border:2px solid var(--retro-line)!important;
			border-radius:12px!important;
			font-weight:700!important;
			letter-spacing:.02em;
			box-shadow:4px 4px 0 rgba(29,31,36,.9)!important;
			transition:transform .16s ease, box-shadow .16s ease, filter .16s ease;
		}
		.btn:hover{
			transform:translate(-1px,-1px);
			box-shadow:5px 5px 0 rgba(29,31,36,.9)!important;
		}
		.btn:active{
			transform:translate(2px,2px);
			box-shadow:2px 2px 0 rgba(29,31,36,.9)!important;
		}
		.btn.bg-gradient-primary,
		.btn.btn-primary{
			background:linear-gradient(135deg,#f58634,#d86729)!important;
			color:#1f1a14!important;
		}
		.btn.bg-gradient-secondary,
		.btn.btn-secondary{
			background:linear-gradient(135deg,#8ac7df,#4f8aa3)!important;
			color:#10151b!important;
		}
		.btn-outline-dark{
			background:#efe3d2!important;
			color:#1f232b!important;
		}
		.btn-outline-light{
			background:#fff2dd!important;
			color:#1f232b!important;
		}
		.btn-outline-primary{
			background:#e8f2ff!important;
			color:#114f8d!important;
		}
		.btn-outline-success{
			background:#e8f7e8!important;
			color:#1f6f3c!important;
		}
		.btn-outline-warning{
			background:#fff3d8!important;
			color:#8e5d10!important;
		}
		.btn-outline-danger{
			background:#ffe6e2!important;
			color:#9a2f24!important;
		}
		.btn-outline-info{
			background:#e6f7ff!important;
			color:#1f6f8b!important;
		}
		.form-control,.form-select{
			border:2px solid var(--retro-line)!important;
			border-radius:11px!important;
			background:#fffdf9!important;
			color:var(--retro-ink)!important;
			font-family:"IBM Plex Mono",monospace;
		}
		.form-control:focus,.form-select:focus{
			box-shadow:0 0 0 .2rem rgba(216,103,41,.2)!important;
			border-color:var(--retro-accent)!important;
		}
		.rounded-pill{
			border-radius:11px!important;
		}
		.table-borderless>:not(caption)>*>*{
			border-bottom-width:1px!important;
		}
		.alert{
			border:2px solid var(--retro-line)!important;
			border-radius:12px!important;
			box-shadow:var(--retro-shadow-soft)!important;
		}
		.list-group-item{
			background:rgba(255, 250, 243, .86)!important;
			border-color:rgba(31,35,43,.15)!important;
		}
		.input-group-text{
			border:2px solid var(--retro-line)!important;
			background:#f6eadb!important;
			color:var(--retro-ink)!important;
		}
		.dataTables_wrapper .dataTables_filter input,
		.dataTables_wrapper .dataTables_length select{
			border:2px solid var(--retro-line)!important;
			border-radius:10px!important;
			background:#fffdf9!important;
			padding:.35rem .55rem;
			font-family:"IBM Plex Mono",monospace;
		}
		.dataTables_wrapper .dataTables_paginate .pagination{
			gap:.2rem;
			align-items:center;
			flex-wrap:wrap;
		}
		.dataTables_wrapper .dataTables_paginate .page-item .page-link{
			border:2px solid var(--retro-line)!important;
			border-radius:10px!important;
			background:#fff5e7!important;
			color:var(--retro-ink)!important;
			box-shadow:2px 2px 0 rgba(29,31,36,.5)!important;
			margin:0!important;
			min-width:2.2rem;
			text-align:center;
		}
		.dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
		.dataTables_wrapper .dataTables_paginate .page-item .page-link:hover{
			background:linear-gradient(135deg,#ffd27b,#f58634)!important;
			color:#23180e!important;
		}
		.dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link{
			opacity:.55;
			box-shadow:none!important;
		}
		.dataTables_wrapper .dataTables_info{
			padding-top:.75rem;
			font-family:"IBM Plex Mono",monospace;
			font-size:.82rem;
		}
		.card .h6,
		.card .h5,
		.card h2,
		.card h5,
		.card h6{
			letter-spacing:.01em;
		}
		footer{
			background:#fff5e9!important;
			border-top:2px solid var(--retro-line)!important;
			box-shadow:0 -3px 0 rgba(20,22,29,.6);
		}
		.text-secondary,.text-muted{color:var(--retro-muted)!important;}
		@keyframes retroContentIn{
			from{opacity:0;transform:translateY(12px)}
			to{opacity:1;transform:translateY(0)}
		}
		@keyframes retroModalIn{
			from{opacity:0;transform:translateY(12px) scale(.985)}
			to{opacity:1;transform:translateY(0) scale(1)}
		}
		@keyframes retroDropdownIn{
			from{opacity:0;transform:translateY(-6px) scale(.98)}
			to{opacity:1;transform:translateY(0) scale(1)}
		}
			@media (max-width:768px){
				.navbar{margin:8px 8px 0}
				.card,.modal-content{box-shadow:4px 4px 0 rgba(20,22,29,.86)!important}
				.btn{box-shadow:3px 3px 0 rgba(29,31,36,.9)!important}
				#content.container{
					padding:.75rem!important;
					padding-top:1rem!important;
					margin-bottom:.75rem!important;
				}
				.card-header{padding:.75rem .9rem!important}
				.card-body{padding:.9rem!important}
				.btn{
					font-size:.82rem!important;
					padding:.45rem .7rem!important;
				}
				.table{font-size:.84rem}
				.modal-dialog{margin:.7rem}
			}
			</style>
    
</head>
<style>
 .hacks {
          position: relative;
          display: inline-block;
          width: 100%;
          height: 20px;
          float: left;
          margin: 5%;
        }
        .switch {
          position: relative;
          display: inline-block;
          width: 40px;
          height: 20px;
          float: right-end;
          align-items: flex-end;
          margin: 5px 5px 5px 5px;
        }
        
        /* Hide default HTML checkbox */
        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }
        
        /* The slider */
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        .slider:before {
          position: absolute;
          content: "";
          height: 12px;
          width: 12px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        input:checked + .slider {
          background-color: #2196F3;
        }
        
        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }
        
        input:checked + .slider:before {
          -webkit-transform: translateX(20px);
          -ms-transform: translateX(20px);
          transform: translateX(20px);
        }
        
        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }
        
	        .slider.round:before {
	          border-radius: 50%;
	        }
	        main {
	          padding-bottom: 6rem;
	        }
	        @media (max-width: 768px) {
	          main {
	            padding-bottom: 1.25rem;
	          }
	          .fixed-bottom {
	            position: static !important;
	          }
	        }
    </style>
<body>
    <div id="loader">
			<img src="https://i.pinimg.com/originals/2c/bb/5e/2cbb5e95b97aa2b496f6eaec84b9240d.gif"/>
		</div>
    <!-- Start menu -->
    <?= $this->include('Layout/Header') ?>
    <!-- End of menu -->
    <main>
        <div class="container p-3 py-4 mb-3" id="content">
            <!-- Start content -->
            <?= $this->renderSection('content') ?>
            <!-- End of content -->
        </div>
    </main>
    <?= $this->include('Layout/mod_hub_modal') ?>
    <footer class="fixed-bottom bg-body border-top py-3 text-muted">
        <div class="container">
            <small class="text-muted">&copy; <?= date('Y') ?> - <?= BASE_NAME ?></small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.0/sweetalert2.all.min.js" integrity="sha512-0UUEaq/z58JSHpPgPv8bvdhHFRswZzxJUT9y+Kld5janc9EWgGEVGfWV1hXvIvAJ8MmsR5d4XV9lsuA90xXqUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?= script_tag('assets/js/natacode.js') ?>

    <?= $this->renderSection('js') ?>

</body>

</html>
