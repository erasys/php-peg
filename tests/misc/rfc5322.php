<?php
require_once __DIR__.'/../autoloader.php';

use hafriedlander\Peg\Compiler;

$peg = __DIR__.'/../../examples/Rfc5322.peg.php';
$output = __DIR__.'/Rfc5322Parser.php';

$code = Compiler::compile(file_get_contents($peg));
file_put_contents($output, $code);
require $output;

//$p = new Rfc822(<<<EOS
//Mary Smith <mary@x.test>, jdoe@example.org, Who? <one@y.test>, <boss@nil.test>, "Giant; \"Big\" Box" <sysservices@example.net>, A Group:Ed Jones <c@a.test>, Undisclosed recipients:, Pete <pete@silly.example>
//EOS
//);
$header = iconv_mime_decode(<<<EOS
"Adrien Merer" <adrien_merer@yahoo.fr>, 
 Alice Gestin <detonnant@riseup.net>,
 =?ISO-8859-1?Q?Andr=E9e_Bernable?= <a.bernable@free.fr>, 
 =?ISO-8859-1?Q?Aur=E9lien_Arnoux?= <aurelienarnoux76@gmail.com>, 
 Bastien <bastien.loufrani@gmail.com>,
 Benjamin Rando <randobenjamin@gmail.com>, 
 Blandine <blandine@lembobineuse.biz>,
 =?ISO-8859-1?Q?C=E9line_Gauthier?= <piou.neuf@laposte.net>, 
 Chantal Vasseur <chantal@lembobineuse.biz>,
 charles fichaux <charles.fichaux@gmail.com>, =?ISO-8859-1?Q?Chlo=E9_Bl?=
 =?ISO-8859-1?Q?ondeau?= <fridaoblong@netcourrier.com>, 
 Christophe Higli <goulag1@hotmail.fr>,
 Cyril Athony <cissou22@gmail.com>, 
 Cyril Benhamou <cyrilbenhamou@free.fr>,
 Damien Ravnich <damienravnich@gmail.com>, David Bausseron <db68@free.fr>, 
 David Merlo <damerlo@hotmail.fr>,
 David Thalet <soundatamail@yahoo.fr>, 
 Didier Simione <omnimars@hotmail.com>,
 Elodie LE BREUT <elodie@amicentre.biz>, 
 Emilie Allais <pole.institimag@wanadoo.fr>,
 Emilie Lesbros <emilielesbros@gmail.com>, 
 Emmanuel Cremer <manuchello@gmail.com>,
 Enzo Lavigne <enzo.lavigne@gmail.com>, 
 Erik Billabert <e.billabert@grim-marseille.com>,
 Francois Rossi <rossi.francois@gmail.com>, 
 Francois Rossi <rossi.francois@gmail.com>,
 Gaelle Jeandon <gaelle.jeandon@gmail.com>, 
 Holly Manyak <h.manyak@sfr.fr>,
 Hugues Viellot <lafrichetabarnak@yahoo.fr>, Jako <dure-mere@hotmail.fr>, 
 =?ISO-8859-1?Q?J=E9r=E9my_Laffon?= <info.jeremylaffon@gmail.com>, 
 Johanna Tzipkine <johanna@grenouille888.org>,
 Johanna Tzipkine <johanna@grenouille888.org>, 
 Jules Bernable <jules.bernable@gmail.com>,
 Jules Bernable <julius@laforcemolle.org>, 
 Julien Combette <julcodots@wanadoo.fr>,
 Krim Bouslama <krimomo@hotmail.fr>, laforcemolle@gmail.com, 
 La Meson <contact@lameson.com>,
 Lionel Spiga <lionel@leposteagalene.com>, 
 Macario <madamemacario@free.fr>,
 Magalie Arnoux <magooze@hotmail.fr>, 
 Marc Hernandez <tro.glodyte@laposte.net>,
 Martial <d2r2.off@gmail.com>, Maxime <funkforever@gmail.com>, 
 Naomi Jean <naomi.jean@gmail.com>,
 Nicolas Delorme <nicolasdelorme@orange.fr>, 
 Nicolas Fouquet <lartisanduson@live.fr>,
 Nicolas Fouquet <lartisanduson@live.fr>, 
 Nicolas Hobbes <hobbenico@yahoo.fr>,
 Nicolas Martin <nicolashansmartin@yahoo.fr>, Olivier <ing.mars@free.fr>, 
 Olivier <olivier.lacroix4@gmail.com>,
 Olivier Villefranche <info@oliviervillefranche.fr>, 
 Paco Rodriguez <pacodhapunx@yahoo.com>,
 Philippe Renault <filreno@orange.fr>, =?ISO-8859-1?Q?R=E9my_Jouffroy?=
 <jouffroyremy@yahoo.fr>, Romain Cricquet <the_simple@hotmail.fr>, 
 Samia <contact@lamerveilleuse.org>,
 Simon City Weezle <cityweezle@gmail.com>, 
 Sophie Choupas <kooliebar@hotmail.com>,
 Sylvain Quatreville <sylvain@lembobineuse.biz>, 
 Thierry Noagues <tenn@free.fr>,
 Will Turner-Duffin <willaiden@hotmail.com>, =?ISO-8859-1?Q?Xu=E2n_Bera?=
 =?ISO-8859-1?Q?rd?= <xiuxiux@hotmail.fr>, 
 Yasmine Blum <yasblum99@hotmail.fr>,
 Yves Bernable <y.bernable@free.fr>
EOS
, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, "utf-8");

var_dump($header);


$p = new Rfc5322();
print_r( $p->match_address_list() ) ;
