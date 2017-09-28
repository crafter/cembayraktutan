<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Cem Bayraktutan</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				color: #ffffff;
				font-family:Monospace;
				font-size:13px;
				text-align:center;
				font-weight: bold;
				background-color: #000000;
				margin: 0px;
				overflow: hidden;
			}
			#info {
				color: #fff;
				position: absolute;
				top: 0px; width: 100%;
				padding: 5px;
				z-index:100;
			}
		</style>
	</head>

	<body>
		<div id="info"><a href="http://threejs.org" target="_blank" rel="noopener">three.js</a> - custom attributes example</div>
		<div id="container"></div>

		<script src="js/Detector.js"></script>
		<script src="js/libs/stats.min.js"></script>

		<script src="build/three.js"></script>

		<script type="x-shader/x-vertex" id="vertexshader">
			uniform float amplitude;
			attribute vec3 displacement;
			attribute vec3 customColor;
			varying vec3 vColor;
			void main() {
				vec3 newPosition = position + amplitude * displacement;
				vColor = customColor;
				gl_Position = projectionMatrix * modelViewMatrix * vec4( newPosition, 1.0 );
			}
		</script>

		<script type="x-shader/x-fragment" id="fragmentshader">
			uniform vec3 color;
			uniform float opacity;
			varying vec3 vColor;
			void main() {
				gl_FragColor = vec4( vColor * color, opacity );
			}
		</script>


		<script>
		if ( ! Detector.webgl ) Detector.addGetWebGLMessage();
		var renderer, scene, camera, stats;
		var object, uniforms;
		var loader = new THREE.FontLoader();
		loader.load( 'fonts/helvetiker_bold.typeface.json', function ( font ) {
			init( font );
			animate();
		} );
		function init( font ) {
			camera = new THREE.PerspectiveCamera( 30, window.innerWidth / window.innerHeight, 1, 10000 );
			camera.position.z = 400;
			scene = new THREE.Scene();
			scene.background = new THREE.Color( 0x050505 );
			uniforms = {
				amplitude: { value: 5.0 },
				opacity:   { value: 0.3 },
				color:     { value: new THREE.Color( 0xff0000 ) }
			};
			var shaderMaterial = new THREE.ShaderMaterial( {
				uniforms:       uniforms,
				vertexShader:   document.getElementById( 'vertexshader' ).textContent,
				fragmentShader: document.getElementById( 'fragmentshader' ).textContent,
				blending:       THREE.AdditiveBlending,
				depthTest:      false,
				transparent:    true
			});
			var geometry = new THREE.TextGeometry( 'Cem BYRAKTUTAN', {
				font: font,
				size: 50,
				height: 15,
				curveSegments: 10,
				bevelThickness: 5,
				bevelSize: 1.5,
				bevelEnabled: true,
				bevelSegments: 10,
				steps: 40
			} );
			geometry.center();
			var vertices = geometry.vertices;
			var buffergeometry = new THREE.BufferGeometry();
			var position = new THREE.Float32BufferAttribute( vertices.length * 3, 3 ).copyVector3sArray( vertices );
			buffergeometry.addAttribute( 'position', position );
			var displacement = new THREE.Float32BufferAttribute( vertices.length * 3, 3 );
			buffergeometry.addAttribute( 'displacement', displacement );
			var customColor = new THREE.Float32BufferAttribute( vertices.length * 3, 3 );
			buffergeometry.addAttribute( 'customColor', customColor );
			var color = new THREE.Color( 0xffffff );
			for( var i = 0, l = customColor.count; i < l; i ++ ) {
				color.setHSL( i / l, 0.5, 0.5 );
				color.toArray( customColor.array, i * customColor.itemSize );
			}
			object = new THREE.Line( buffergeometry, shaderMaterial );
			object.rotation.x = 0.2;
			scene.add( object );
			renderer = new THREE.WebGLRenderer( { antialias: true } );
			renderer.setPixelRatio( window.devicePixelRatio );
			renderer.setSize( window.innerWidth, window.innerHeight );
			var container = document.getElementById( 'container' );
			container.appendChild( renderer.domElement );
			stats = new Stats();
			container.appendChild( stats.dom );
			//
			window.addEventListener( 'resize', onWindowResize, false );
		}
		function onWindowResize() {
			camera.aspect = window.innerWidth / window.innerHeight;
			camera.updateProjectionMatrix();
			renderer.setSize( window.innerWidth, window.innerHeight );
		}
		function animate() {
			requestAnimationFrame( animate );
			render();
			stats.update();
		}
		function render() {
			var time = Date.now() * 0.001;
			object.rotation.y = 0.25 * time;
			uniforms.amplitude.value = Math.sin( 0.5 * time );
			uniforms.color.value.offsetHSL( 0.0005, 0, 0 );
			var attributes = object.geometry.attributes;
			var array = attributes.displacement.array;
			for ( var i = 0, l = array.length; i < l; i += 3 ) {
				array[ i     ] += 0.3 * ( 0.5 - Math.random() );
				array[ i + 1 ] += 0.3 * ( 0.5 - Math.random() );
				array[ i + 2 ] += 0.3 * ( 0.5 - Math.random() );
			}
			attributes.displacement.needsUpdate = true;
			renderer.render( scene, camera );
		}
	</script>

</body>

</html>
