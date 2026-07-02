// @ts-nocheck

$(document).ready(function () {
    if ($('.google-fonts-dropdown').length > 0) {
        console.log('Fetching Google Fonts...');
        $.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBwIX97bVWr3-6AIUvGkcNnmFgirefZ6Sw')
            .done(function (data) {
                console.log('Fonts fetched successfully:', data.items.length);
                $.each(data.items, function (index, font) {
                    $('.google-fonts-dropdown').append($('<option></option>').attr('value', font.family).text(font.family));
                });

                $('.google-fonts-dropdown').each(function () {
                    var selected = $(this).data('selected');
                    if (selected) {
                        $(this).val(selected);
                    }
                });

                // Build custom dropdown UI
                buildCustomFontDropdowns();

                // Initialize custom font search
                initializeFontSearch();
            })
            .fail(function (jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                console.error("Request Failed: " + err);
                // Fallback to a few popular fonts if API fails
                var fallbackFonts = [
                    "ABeeZee",
                    "Abel",
                    "Abhaya Libre",
                    "Aboreto",
                    "Abril Fatface",
                    "Abyssinica SIL",
                    "Aclonica",
                    "Acme",
                    "Actor",
                    "Adamina",
                    "Advent Pro",
                    "Agbalumo",
                    "Agdasima",
                    "Agu Display",
                    "Akatab",
                    "Akaya Kanadaka",
                    "Akaya Telivigala",
                    "Akronim",
                    "Akshar",
                    "Aladin",
                    "Alata",
                    "Alatsi",
                    "Albert Sans",
                    "Aldrich",
                    "Alef",
                    "Alegreya",
                    "Alegreya SC",
                    "Alegreya Sans",
                    "Alegreya Sans SC",
                    "Aleo",
                    "Alex Brush",
                    "Alexandria",
                    "Alfa Slab One",
                    "Alice",
                    "Alike",
                    "Alike Angular",
                    "Alkalami",
                    "Alkatra",
                    "Allan",
                    "Allerta",
                    "Allerta Stencil",
                    "Allison",
                    "Allura",
                    "Almarai",
                    "Almendra",
                    "Almendra Display",
                    "Almendra SC",
                    "Alumni Sans",
                    "Alumni Sans Collegiate One",
                    "Alumni Sans Inline One",
                    "Alumni Sans Pinstripe",
                    "Amarante",
                    "Amaranth",
                    "Amatic SC",
                    "Amethysta",
                    "Amiko",
                    "Amiri",
                    "Amiri Quran",
                    "Amita",
                    "Anaheim",
                    "Andada Pro",
                    "Andika",
                    "Angkor",
                    "Annie Use Your Telescope",
                    "Anonymous Pro",
                    "Antic",
                    "Antic Didone",
                    "Antic Slab",
                    "Anton",
                    "Antonio",
                    "Anuphan",
                    "Anybody",
                    "Aoboshi One",
                    "Aref Ruqaa",
                    "Aref Ruqaa Ink",
                    "Arima",
                    "Arimo",
                    "Arizonia",
                    "Armata",
                    "Arsenal",
                    "Artifika",
                    "Arvo",
                    "Arya",
                    "Asap",
                    "Asap Condensed",
                    "Asar",
                    "Asset",
                    "Assistant",
                    "Astloch",
                    "Asul",
                    "Athiti",
                    "Atkinson Hyperlegible",
                    "Atma",
                    "Atomic Age",
                    "Aubrey",
                    "Audiowide",
                    "Autour One",
                    "Average",
                    "Average Sans",
                    "Averia Gruesa Libre",
                    "Averia Libre",
                    "Averia Sans Libre",
                    "Averia Serif Libre",
                    "Azeret Mono",
                    "B612",
                    "B612 Mono",
                    "BIZ UDGothic",
                    "BIZ UDMincho",
                    "BIZ UDPGothic",
                    "BIZ UDPMincho",
                    "Babylonica",
                    "Bacasime Antique",
                    "Bad Script",
                    "Bagel Fat One",
                    "Bahiana",
                    "Bahianita",
                    "Bai Jamjuree",
                    "Bakbak One",
                    "Ballet",
                    "Baloo 2",
                    "Baloo Bhai 2",
                    "Baloo Bhaijaan 2",
                    "Baloo Bhaina 2",
                    "Baloo Chettan 2",
                    "Baloo Da 2",
                    "Baloo Paaji 2",
                    "Baloo Tamma 2",
                    "Baloo Tammudu 2",
                    "Baloo Thambi 2",
                    "Balsamiq Sans",
                    "Balthazar",
                    "Bangers",
                    "Barlow",
                    "Barlow Condensed",
                    "Barlow Semi Condensed",
                    "Barriecito",
                    "Barrio",
                    "Basic",
                    "Baskervville",
                    "Battambang",
                    "Baumans",
                    "Bayon",
                    "Be Vietnam Pro",
                    "Beau Rivage",
                    "Bebas Neue",
                    "Belanosima",
                    "Belgrano",
                    "Bellefair",
                    "Belleza",
                    "Bellota",
                    "BenchNine",
                    "Benne",
                    "Bentham",
                    "Berkshire Swash",
                    "Besley",
                    "Beth Ellen",
                    "Bigelow Rules",
                    "Bigshot One",
                    "Bilbo",
                    "Bilbo Swash Caps",
                    "BioRhyme",
                    "BioRhyme Expanded",
                    "Birthstone",
                    "Birthstone Bounce",
                    "Biryani",
                    "Bitter",
                    "Black And White Picture",
                    "Black Han Sans",
                    "Black Ops One",
                    "Blaka",
                    "Blaka Hollow",
                    "Blaka Ink",
                    "Blinker",
                    "Bodoni Moda",
                    "Bokor",
                    "Boogaloo",
                    "Borel",
                    "Bowlby One",
                    "Bowlby One SC",
                    "Braah One",
                    "Brawler",
                    "Bree Serif",
                    "Bricolage Grotesque",
                    "Bruno Ace",
                    "Bruno Ace SC",
                    "Buda",
                    "Buenard",
                    "Bungee",
                    "Bungee Hairline",
                    "Bungee Inline",
                    "Bungee Outline",
                    "Bungee Shade",
                    "Bungee Spice",
                    "Butcherman",
                    "Butterfly Kids",
                    "Cabin",
                    "Cabin Condensed",
                    "Cabin Sketch",
                    "Caesar Dressing",
                    "Cagliostro",
                    "Cairo",
                    "Cairo Play",
                    "Caladea",
                    "Calistoga",
                    "Calligraffitti",
                    "Cambay",
                    "Cambo",
                    "Candal",
                    "Cantarell",
                    "Cantata One",
                    "Cantora One",
                    "Caprasimo",
                    "Capriola",
                    "Caramel",
                    "Carattere",
                    "Cardo",
                    "Carlito",
                    "Carme",
                    "Carrois Gothic",
                    "Carrois Gothic SC",
                    "Castoro",
                    "Castoro Titling",
                    "Catamaran",
                    "Caudex",
                    "Caveat",
                    "Caveat Brush",
                    "Cedarville Cursive",
                    "Ceviche One",
                    "Chakra Petch",
                    "Changa",
                    "Changa One",
                    "Chango",
                    "Charis SIL",
                    "Charm",
                    "Charmonman",
                    "Chathura",
                    "Chau Philomene One",
                    "Chela One",
                    "Chelsea Market",
                    "Chenla",
                    "Cherish",
                    "Cherry Bomb One",
                    "Cherry Cream Soda",
                    "Cherry Swash",
                    "Chewy",
                    "Chicle",
                    "Chilanka",
                    "Chivo",
                    "Chivo Mono",
                    "Chokokutai",
                    "Chonburi",
                    "Cinzel",
                    "Cinzel Decorative",
                    "Clicker Script",
                    "Climate Crisis",
                    "Coda",
                    "Coda Caption",
                    "Coiny",
                    "Combo",
                    "Comfortaa",
                    "Comforter",
                    "Comforter Brush",
                    "Comic Neue",
                    "Coming Soon",
                    "Comme",
                    "Commissioner",
                    "Concert One",
                    "Condiment",
                    "Content",
                    "Contrail One",
                    "Convergence",
                    "Cookie",
                    "Copse",
                    "Corben",
                    "Corinthia",
                    "Cormorant",
                    "Cormorant Garamond",
                    "Cormorant Infant",
                    "Cormorant SC",
                    "Cormorant Unicase",
                    "Cormorant Upright",
                    "Courgette",
                    "Courier Prime",
                    "Cousine",
                    "Coustard",
                    "Covered By Your Grace",
                    "Crafty Girls",
                    "Creepster",
                    "Crete Round",
                    "Crimson Pro",
                    "Crimson Text",
                    "Croissant One",
                    "Crushed",
                    "Cuprum",
                    "Cute Font",
                    "Cutive",
                    "Cutive Mono",
                    "DM Mono",
                    "DM Sans",
                    "DM Serif Display",
                    "DM Serif Text",
                    "Dai Banna SIL",
                    "Damion",
                    "Dancing Script",
                    "Danfo",
                    "Dangrek",
                    "Darker Grotesque",
                    "Darumadrop One",
                    "David Libre",
                    "Dawning of a New Day",
                    "Days One",
                    "Dekko",
                    "Dela Gothic One",
                    "Delicious Handrawn",
                    "Delius",
                    "Delius Swash Caps",
                    "Delius Unicase",
                    "Della Respira",
                    "Denk One",
                    "Devonshire",
                    "Dhurjati",
                    "Didact Gothic",
                    "Diphylleia",
                    "Diplomata",
                    "Diplomata SC",
                    "Do Hyeon",
                    "Dokdo",
                    "Domine",
                    "Donegal One",
                    "Dongle",
                    "Doppio One",
                    "Dorsa",
                    "Doto",
                    "Duru Sans",
                    "DynaPuff",
                    "Eagle Lake",
                    "East Sea Dokdo",
                    "Eater",
                    "Economica",
                    "Eczar",
                    "El Messiri",
                    "Electrolize",
                    "Elsie",
                    "Elsie Swash Caps",
                    "Emblema One",
                    "Emilys Candy",
                    "Encode Sans",
                    "Encode Sans Condensed",
                    "Encode Sans Expanded",
                    "Encode Sans SC",
                    "Encode Sans Semi Condensed",
                    "Encode Sans Semi Expanded",
                    "Engagement",
                    "Englebert",
                    "Enriqueta",
                    "Ephesis",
                    "Epilogue",
                    "Erica One",
                    "Esteban",
                    "Estonia",
                    "Euphoria Script",
                    "Ewert",
                    "Exo",
                    "Exo 2",
                    "Expletus Sans",
                    "Explora",
                    "Fahkwang",
                    "Familjen Grotesk",
                    "Fanwood Text",
                    "Farro",
                    "Faustina",
                    "Federant",
                    "Federo",
                    "Felipa",
                    "Fenix",
                    "Festive",
                    "Figtree",
                    "Finlandica",
                    "Fira Code",
                    "Fira Mono",
                    "Fira Sans",
                    "Fira Sans Condensed",
                    "Fira Sans Extra Condensed",
                    "Fjalla One",
                    "Fjord One",
                    "Flamenco",
                    "Flavors",
                    "Fleur De Leah",
                    "Flow Block",
                    "Flow Circular",
                    "Flow Rounded",
                    "Foldit",
                    "Fontdiner Swanky",
                    "Forum",
                    "Fragment Mono",
                    "Francois One",
                    "Frank Ruhl Libre",
                    "Fraunces",
                    "Freckle Face",
                    "Fredericka the Great",
                    "Fredoka",
                    "Freehand",
                    "Fresca",
                    "Frijole",
                    "Fruktur",
                    "Fugaz One",
                    "Fuggles",
                    "Fuzzy Bubbles",
                    "GFS Didot",
                    "GFS Neohellenic",
                    "Ga Maamli",
                    "Gabriela",
                    "Gaegu",
                    "Gafata",
                    "Gajraj One",
                    "Galada",
                    "Galdeano",
                    "Galindo",
                    "Gamja Flower",
                    "Gantari",
                    "Gasoek One",
                    "Gayathri",
                    "Gelasio",
                    "Gemunu Libre",
                    "Genos",
                    "Gentium Book Plus",
                    "Gentium Plus",
                    "Geo",
                    "Geologica",
                    "Georama",
                    "Geostar",
                    "Geostar Fill",
                    "Germania One",
                    "Gideon Roman",
                    "Gidugu",
                    "Gilda Display",
                    "Girassol",
                    "Give You Glory",
                    "Glass Antiqua",
                    "Glegoo",
                    "Gloock",
                    "Gloria Hallelujah",
                    "Glory",
                    "Gluten",
                    "Goblin One",
                    "Gochi Hand",
                    "Goldman",
                    "Golos Text",
                    "Gorditas",
                    "Gothic A1",
                    "Gotu",
                    "Goudy Bookletter 1911",
                    "Goudy Starved",
                    "Graduate",
                    "Grand Hotel",
                    "Grandstander",
                    "Grape Nuts",
                    "Gravitas One",
                    "Great Vibes",
                    "Grechen Fuemen",
                    "Grenze",
                    "Grenze Gotisch",
                    "Grey Qo",
                    "Griffy",
                    "Gruppo",
                    "Gudea",
                    "Gugi",
                    "Gulzar",
                    "Gupter",
                    "Gurajada",
                    "Gwendolyn",
                    "Gyuvetch",
                    "Habibi",
                    "Hachi Maru Pop",
                    "Hahmlet",
                    "Halant",
                    "Hammersmith One",
                    "Hanalei",
                    "Hanalei Fill",
                    "Handjet",
                    "Handlee",
                    "Hanken Grotesk",
                    "Hannari",
                    "Hanuman",
                    "Happy Monkey",
                    "Harmattan",
                    "Headland One",
                    "Heebo",
                    "Henny Penny",
                    "Hepta Slab",
                    "Herr Von Muellerhoff",
                    "Hi Melody",
                    "Hina Mincho",
                    "Hind",
                    "Hind Guntur",
                    "Hind Madurai",
                    "Hind Siliguri",
                    "Hind Vadodara",
                    "Holtwood One SC",
                    "Homemade Apple",
                    "Homenaje",
                    "Hubballi",
                    "Hurricane",
                    "IBM Plex Mono",
                    "IBM Plex Sans",
                    "IBM Plex Sans Arabic",
                    "IBM Plex Sans Condensed",
                    "IBM Plex Sans Devanagari",
                    "IBM Plex Sans Hebrew",
                    "IBM Plex Sans JP",
                    "IBM Plex Sans KR",
                    "IBM Plex Sans Thai",
                    "IBM Plex Sans Thai Looped",
                    "IBM Plex Serif",
                    "Ibarra Real Nova",
                    "Iceberg",
                    "Iceland",
                    "Imbue",
                    "Imperial Script",
                    "Imprima",
                    "Inconsolata",
                    "Inder",
                    "Indie Flower",
                    "Ingrid Darling",
                    "Inika",
                    "Inknut Antiqua",
                    "Inria Sans",
                    "Inria Serif",
                    "Inspiration",
                    "Instrument Sans",
                    "Instrument Serif",
                    "Inter",
                    "Inter Tight",
                    "Irish Grover",
                    "Island Moments",
                    "Istok Web",
                    "Italiana",
                    "Italianno",
                    "Itim",
                    "Jacques Francois",
                    "Jacques Francois Shadow",
                    "Jaldi",
                    "Jersey 10",
                    "Jersey 15",
                    "Jersey 20",
                    "Jersey 25",
                    "JetBrains Mono",
                    "Jim Nightshade",
                    "Joan",
                    "Jockey One",
                    "Jolly Lodger",
                    "Jomhuria",
                    "Jomolhari",
                    "Josefin Sans",
                    "Josefin Slab",
                    "Jost",
                    "Joti One",
                    "Jua",
                    "Judson",
                    "Julee",
                    "Julius Sans One",
                    "Junge",
                    "Jura",
                    "K2D",
                    "Kablammo",
                    "Kadwa",
                    "Kaisei Decol",
                    "Kaisei HarunoUmi",
                    "Kaisei Opti",
                    "Kaisei Tokumin",
                    "Kalam",
                    "Kameron",
                    "Kanit",
                    "Kantumruy Pro",
                    "Karantina",
                    "Karma",
                    "Katibeh",
                    "Kaushan Script",
                    "Kavoon",
                    "Kay Pho Du",
                    "Kdam Thmor Pro",
                    "Keania One",
                    "Kelly Slab",
                    "Kenia",
                    "Khand",
                    "Khmer",
                    "Khula",
                    "Kings",
                    "Kirang Haerang",
                    "Kite One",
                    "Kiwi Maru",
                    "Klee One",
                    "Kleerup",
                    "Knewave",
                    "KoHo",
                    "Kodchasan",
                    "Koh Santepheap",
                    "Kolker Brush",
                    "Kosugi",
                    "Kosugi Maru",
                    "Kotta One",
                    "Koulen",
                    "Kranky",
                    "Kreon",
                    "Kristi",
                    "Krona One",
                    "Kufam",
                    "Kulim Park",
                    "Kumar One",
                    "Kumar One Outline",
                    "Kumbh Sans",
                    "Kurale",
                    "La Belle Aurore",
                    "Labrada",
                    "Lacquer",
                    "Laila",
                    "Lakki Reddy",
                    "Lalezar",
                    "Lancelot",
                    "Langar",
                    "Lato",
                    "Lavishly Yours",
                    "League Gothic",
                    "League Script",
                    "League Spartan",
                    "Leckerli One",
                    "Ledger",
                    "Lekton",
                    "Lemon",
                    "Lemon Tuesday",
                    "Lexend",
                    "Lexend Deca",
                    "Lexend Exa",
                    "Lexend Giga",
                    "Lexend Mega",
                    "Lexend Peta",
                    "Lexend Tera",
                    "Lexend Zetta",
                    "Libre Barcode 128",
                    "Libre Barcode 39",
                    "Libre Baskerville",
                    "Libre Bodoni",
                    "Libre Caslon Display",
                    "Libre Caslon Text",
                    "Libre Franklin",
                    "Licorice",
                    "Life Savers",
                    "Lilita One",
                    "Lily Script One",
                    "Limelight",
                    "Linden Hill",
                    "Lisu Bosa",
                    "Literata",
                    "Liu Jian Mao Cao",
                    "Livvic",
                    "Lobster",
                    "Lobster Two",
                    "Londrina Outline",
                    "Londrina Shadow",
                    "Londrina Sketch",
                    "Londrina Solid",
                    "Long Cang",
                    "Lora",
                    "Love Light",
                    "Love Ya Like A Sister",
                    "Loved by the King",
                    "Lovers Quarrel",
                    "Luckiest Guy",
                    "Lugrasimo",
                    "Lumanosimo",
                    "Lunasima",
                    "Lusitana",
                    "Lustria",
                    "Luxurious Roman",
                    "Luxurious Script",
                    "M PLUS 1",
                    "M PLUS 1 Code",
                    "M PLUS 1p",
                    "M PLUS 2",
                    "M PLUS Code Latin",
                    "M PLUS Rounded 1c",
                    "Ma Shan Zheng",
                    "Macondo",
                    "Macondo Swash Caps",
                    "Mada",
                    "Madimi One",
                    "Magra",
                    "Maiden Orange",
                    "Maitree",
                    "Major Mono Display",
                    "Mako",
                    "Mali",
                    "Mallanna",
                    "Mandali",
                    "Manjari",
                    "Manrope",
                    "Mansalva",
                    "Manuale",
                    "Marcellus",
                    "Marcellus SC",
                    "Marck Script",
                    "Margarine",
                    "Marhey",
                    "Markazi Text",
                    "Marko One",
                    "Marmelad",
                    "Martel",
                    "Martel Sans",
                    "Marvel",
                    "Mate",
                    "Mate SC",
                    "Maven Pro",
                    "McLaren",
                    "Mea Culpa",
                    "Meddon",
                    "Medula One",
                    "Meera Inimai",
                    "Megrim",
                    "Meie Script",
                    "Meow Script",
                    "Merienda",
                    "Merriweather",
                    "Merriweather Sans",
                    "Metal",
                    "Metal Mania",
                    "Metamorphous",
                    "Metrophobic",
                    "Michroma",
                    "Milonga",
                    "Miltonian",
                    "Miltonian Tattoo",
                    "Mina",
                    "Mingzat",
                    "Miniver",
                    "Miriam Libre",
                    "Mirza",
                    "Miss Fajardose",
                    "Mitr",
                    "Mochiy Pop One",
                    "Mochiy Pop P One",
                    "Modak",
                    "Modern Antiqua",
                    "Mogra",
                    "Mohave",
                    "Moirai One",
                    "Molengo",
                    "Mooli",
                    "Moul",
                    "Moulpali",
                    "Mountains of Christmas",
                    "Mouse Memoirs",
                    "Mr Bedfort",
                    "Mr Dafoe",
                    "Mr De Haviland",
                    "Mrs Saint Delafield",
                    "Mrs Sheppards",
                    "Ms Madi",
                    "Mukta",
                    "Mukta Mahee",
                    "Mukta Malar",
                    "Mukta Vaani",
                    "Mulish",
                    "Murecho",
                    "MuseoModerno",
                    "My Soul",
                    "Myrada",
                    "NTR",
                    "Nabla",
                    "Namdhinggo",
                    "Nanum Brush Script",
                    "Nanum Gothic",
                    "Nanum Gothic Coding",
                    "Nanum Myeongjo",
                    "Nanum Pen Script",
                    "Navirou",
                    "Nobile",
                    "Nokora",
                    "Norican",
                    "Nosifer",
                    "Notable",
                    "Nothing You Could Do",
                    "Noticia Text",
                    "Noto Sans",
                    "Noto Serif",
                    "Nova Cut",
                    "Nova Flat",
                    "Nova Mono",
                    "Nova Oval",
                    "Nova Round",
                    "Nova Script",
                    "Nova Slim",
                    "Nova Square",
                    "Numans",
                    "Nunito",
                    "Nunito Sans",
                    "Odibee Sans",
                    "Odor Mean Chey",
                    "Offside",
                    "Oi",
                    "Ojuju",
                    "Ole",
                    "Oleo Script",
                    "Oleo Script Swash Caps",
                    "Onest",
                    "Oooh Baby",
                    "Open Sans",
                    "Oranienbaum",
                    "Orbit",
                    "Orbitron",
                    "Oregano",
                    "Orelega One",
                    "Orienta",
                    "Original Surfer",
                    "Oswald",
                    "Outfit",
                    "Over the Rainbow",
                    "Overlock",
                    "Overlock SC",
                    "Overpass",
                    "Overpass Mono",
                    "Ovo",
                    "Oxanium",
                    "Oxygen",
                    "Oxygen Mono",
                    "PT Mono",
                    "PT Sans",
                    "PT Sans Caption",
                    "PT Sans Narrow",
                    "PT Serif",
                    "PT Serif Caption",
                    "Pacifico",
                    "Padauk",
                    "Palanquin",
                    "Palanquin Dark",
                    "Palette Mosaic",
                    "Pangolin",
                    "Paprika",
                    "Parisienne",
                    "Passero One",
                    "Passion One",
                    "Passions Conflict",
                    "Pathway Extreme",
                    "Pathway Gothic One",
                    "Patrick Hand",
                    "Patrick Hand SC",
                    "Pattaya",
                    "Patua One",
                    "Pavanam",
                    "Paytone One",
                    "Peddana",
                    "Peralta",
                    "Permanent Marker",
                    "Petemoss",
                    "Petit Formal Script",
                    "Petrona",
                    "Philosopher",
                    "Piazzolla",
                    "Piedra",
                    "Pinyon Script",
                    "Pirata One",
                    "Plaster",
                    "Play",
                    "Playball",
                    "Playfair Display",
                    "Playfair Display SC",
                    "Playpen Sans",
                    "Playwrite AR",
                    "Playwrite AT",
                    "Playwrite AU NSW",
                    "Playwrite AU QLD",
                    "Playwrite AU SA",
                    "Playwrite AU TAS",
                    "Playwrite AU VIC",
                    "Playwrite BE VLG",
                    "Playwrite BE WAL",
                    "Playwrite BR",
                    "Playwrite CA",
                    "Playwrite CL",
                    "Playwrite CO",
                    "Playwrite CU",
                    "Playwrite CZ",
                    "Playwrite DE Grund",
                    "Playwrite DE LA",
                    "Playwrite DE SAS",
                    "Playwrite DE VA",
                    "Playwrite DK Loopet",
                    "Playwrite DK Uloopet",
                    "Playwrite ES",
                    "Playwrite ES Deco",
                    "Playwrite FR Moderne",
                    "Playwrite FR Trad",
                    "Playwrite GB J",
                    "Playwrite GB S",
                    "Playwrite HR",
                    "Playwrite HR Lijeva",
                    "Playwrite HU",
                    "Playwrite ID",
                    "Playwrite IE",
                    "Playwrite IN",
                    "Playwrite IS",
                    "Playwrite IT Moderna",
                    "Playwrite IT Trad",
                    "Playwrite MX",
                    "Playwrite NG Modern",
                    "Playwrite NL",
                    "Playwrite NO",
                    "Playwrite NZ",
                    "Playwrite PE",
                    "Playwrite PL",
                    "Playwrite PT",
                    "Playwrite RO",
                    "Playwrite SK",
                    "Playwrite TZ",
                    "Playwrite UA",
                    "Playwrite US Modern",
                    "Playwrite US Trad",
                    "Playwrite VN",
                    "Playwrite ZA",
                    "Plus Jakarta Sans",
                    "Podkova",
                    "Poiret One",
                    "Poller One",
                    "Poltawski Nowy",
                    "Poly",
                    "Pompiere",
                    "Pontano Sans",
                    "Poor Story",
                    "Poppins",
                    "Port Lligat Sans",
                    "Port Lligat Slab",
                    "Potta One",
                    "Pragati Narrow",
                    "Praise",
                    "Prata",
                    "Preahvihear",
                    "Press Start 2P",
                    "Pridi",
                    "Princess Sofia",
                    "Prociono",
                    "Prompt",
                    "Prosto One",
                    "Proza Libre",
                    "Public Sans",
                    "Puppies Play",
                    "Puritan",
                    "Purple Purse",
                    "Qahiri",
                    "Quando",
                    "Quantico",
                    "Quattrocento",
                    "Quattrocento Sans",
                    "Questrial",
                    "Quicksand",
                    "Quintessential",
                    "Qwitcher Grypen",
                    "REM",
                    "Racing Sans One",
                    "Radio Canada",
                    "Radio Canada Big",
                    "Rajdhani",
                    "Raleway",
                    "Raleway Dots",
                    "Ramabhadra",
                    "Ramaraja",
                    "Rambla",
                    "Rammetto One",
                    "Rampart One",
                    "Rancho",
                    "Ranga",
                    "Rasa",
                    "Rationale",
                    "Ravi Prakash",
                    "Readex Pro",
                    "Recursive",
                    "Red Hat Display",
                    "Red Hat Mono",
                    "Red Hat Text",
                    "Red Rose",
                    "Reem Kufi",
                    "Reem Kufi Fun",
                    "Reem Kufi Ink",
                    "Reenie Beanie",
                    "Reggae One",
                    "Rethink Sans",
                    "Revalia",
                    "Rhodium Libre",
                    "Ribeye",
                    "Ribeye Marrow",
                    "Righteous",
                    "Risque",
                    "Road Rage",
                    "Roboto",
                    "Roboto Condensed",
                    "Roboto Flex",
                    "Roboto Mono",
                    "Roboto Serif",
                    "Roboto Slab",
                    "Rochester",
                    "Rock 3D",
                    "Rock Salt",
                    "RocknRoll One",
                    "Rokkitt",
                    "Romanesco",
                    "Ropa Sans",
                    "Rosario",
                    "Rosarivo",
                    "Rouge Script",
                    "Rowdies",
                    "Rozha One",
                    "Rubik",
                    "Rubik Mono One",
                    "Rubik One",
                    "Ruda",
                    "Rufina",
                    "Ruge Boogie",
                    "Ruluko",
                    "Rum Raisin",
                    "Ruslan Display",
                    "Russo One",
                    "Sacramento",
                    "Sahitya",
                    "Saira",
                    "Saira Condensed",
                    "Saira Extra Condensed",
                    "Saira Semi Condensed",
                    "Saira Stencil One",
                    "Salsa",
                    "Sanchez",
                    "Sancreek",
                    "Sansita",
                    "Sansita Swashed",
                    "Sarabun",
                    "Sarala",
                    "Sarina",
                    "Sarpanch",
                    "Sassy Frass",
                    "Satisfy",
                    "Sawarabi Gothic",
                    "Sawarabi Mincho",
                    "Scada",
                    "Scheherazade New",
                    "Schibsted Grotesk",
                    "Schoolbell",
                    "Scope One",
                    "Seaweed Script",
                    "Secular One",
                    "Sedgwick Ave",
                    "Sedgwick Ave Display",
                    "Sen",
                    "Send Flowers",
                    "Sevillana",
                    "Seymour One",
                    "Shadows Into Light",
                    "Shadows Into Light Two",
                    "Shalimar",
                    "Shantell Sans",
                    "Shanti",
                    "Share",
                    "Share Tech",
                    "Share Tech Mono",
                    "Shippori Antique",
                    "Shippori Antique B1",
                    "Shippori Mincho",
                    "Shippori Mincho B1",
                    "Shizuru",
                    "Shrikhand",
                    "Siemreap",
                    "Sigmar",
                    "Sigmar One",
                    "Signika",
                    "Signika Negative",
                    "Silkscreen",
                    "Simonetta",
                    "Single Day",
                    "Sintony",
                    "Sirin Stencil",
                    "Six Caps",
                    "Sixtyfour",
                    "Skranji",
                    "Slabo 13px",
                    "Slabo 27px",
                    "Slackey",
                    "Slackside One",
                    "Smokum",
                    "Smooch",
                    "Smooch Sans",
                    "Smythe",
                    "Sniglet",
                    "Snippet",
                    "Snowburst One",
                    "Sofadi One",
                    "Sofia",
                    "Sofia Sans",
                    "Sofia Sans Condensed",
                    "Sofia Sans Extra Condensed",
                    "Sofia Sans Semi Condensed",
                    "Solitreo",
                    "Solway",
                    "Song Myung",
                    "Sono",
                    "Sora",
                    "Sorts Mill Goudy",
                    "Source Code Pro",
                    "Source Sans 3",
                    "Source Serif 4",
                    "Space Grotesk",
                    "Space Mono",
                    "Special Elite",
                    "Spectral",
                    "Spectral SC",
                    "Spicy Rice",
                    "Spinnaker",
                    "Spirax",
                    "Splash",
                    "Spline Sans",
                    "Spline Sans Mono",
                    "Squada One",
                    "Square Peg",
                    "Sree Krushnadevaraya",
                    "Sriracha",
                    "Srisakdi",
                    "Staatliches",
                    "Stalemate",
                    "Stalinist One",
                    "Stardos Stencil",
                    "Stick",
                    "Stick No Bills",
                    "Stint Ultra Condensed",
                    "Stint Ultra Expanded",
                    "Stoke",
                    "Strait",
                    "Style Script",
                    "Stylish",
                    "Sue Ellen Francisco",
                    "Suez One",
                    "Sulphur Point",
                    "Sumana",
                    "Suncatcher Script",
                    "Sunflower",
                    "Sunshiney",
                    "Supermercado One",
                    "Sura",
                    "Suranna",
                    "Suravaram",
                    "Suwannaphum",
                    "Swanky and Moo Moo",
                    "Syncopate",
                    "Tac One",
                    "Tai Heritage Pro",
                    "Tajawal",
                    "Tangerine",
                    "Tapestry",
                    "Taprom",
                    "Tauri",
                    "Taviraj",
                    "Teachers",
                    "Teko",
                    "Tektur",
                    "Telex",
                    "Tenali Ramakrishna",
                    "Tenor Sans",
                    "Text Me One",
                    "Thasadith",
                    "The Girl Next Door",
                    "The Nautigal",
                    "Tienne",
                    "Tillana",
                    "Tilt Neon",
                    "Tilt Prism",
                    "Tilt Warp",
                    "Timmana",
                    "Tinos",
                    "Tiro Bangla",
                    "Tiro Devanagari",
                    "Tiro Gurmukhi",
                    "Tiro Kannada",
                    "Tiro Malayalam",
                    "Tiro Tamil",
                    "Tiro Telugu",
                    "Titan One",
                    "Titillium Web",
                    "Tomorrow",
                    "Tourney",
                    "Trade Winds",
                    "Train One",
                    "Trirong",
                    "Trocchi",
                    "Trochut",
                    "Truculenta",
                    "Trykker",
                    "Tsukimi Rounded",
                    "Tulpen One",
                    "Turret Road",
                    "Twinkle Star",
                    "Ubuntu",
                    "Ubuntu Condensed",
                    "Ubuntu Mono",
                    "Ubuntu Sans",
                    "Ubuntu Sans Mono",
                    "Uchen",
                    "Ultra",
                    "Unbounded",
                    "Uncial Antiqua",
                    "Underdog",
                    "Unica One",
                    "UnifrakturCook",
                    "UnifrakturMaguntia",
                    "Unkempt",
                    "Unlock",
                    "Unna",
                    "Updock",
                    "Urbanist",
                    "VT323",
                    "Vampiro One",
                    "Varela",
                    "Varela Round",
                    "Varta",
                    "Vast Shadow",
                    "Vazirmatn",
                    "Vesper Libre",
                    "Viaoda Libre",
                    "Vibes",
                    "Vibur",
                    "Victor Mono",
                    "Vidaloka",
                    "Viga",
                    "Vollkorn",
                    "Vollkorn SC",
                    "Voltaire",
                    "Waiting for the Sunrise",
                    "Wallpoet",
                    "Walter Turncoat",
                    "Warnes",
                    "Water Brush",
                    "Waterfall",
                    "Wavefont",
                    "Wellfleet",
                    "Wendy One",
                    "Whisper",
                    "WindSong",
                    "Wire One",
                    "Wix Madefor Display",
                    "Wix Madefor Text",
                    "Work Sans",
                    "Workbench",
                    "Xanh Mono",
                    "Yaldevi",
                    "Yanone Kaffeesatz",
                    "Yantramanav",
                    "Yatra One",
                    "Yellowtail",
                    "Yeon Sung",
                    "Yeseva One",
                    "Yesteryear",
                    "Yomogi",
                    "Young Serif",
                    "Yrsa",
                    "Yuji Boku",
                    "Yuji Mai",
                    "Yuji Syuku",
                    "Yusei Magic",
                    "ZCOOL KuaiLe",
                    "ZCOOL QingKe HuangYou",
                    "ZCOOL XiaoWei",
                    "Zen Antique",
                    "Zen Antique Soft",
                    "Zen Dots",
                    "Zen Kaku Gothic Antique",
                    "Zen Kaku Gothic New",
                    "Zen Kurenaido",
                    "Zen Loop",
                    "Zen Maru Gothic",
                    "Zen Old Mincho",
                    "Zen Tokyo Zoo",
                    "Zeyada",
                    "Zhi Mang Xing",
                    "Zilla Slab",
                    "Zilla Slab Highlight"
                ];
                $.each(fallbackFonts, function (index, font) {
                    $('.google-fonts-dropdown').append($('<option></option>').attr('value', font).text(font));
                });
                $('.google-fonts-dropdown').each(function () {
                    var selected = $(this).data('selected');
                    if (selected) $(this).val(selected);
                });

                // Build custom dropdown UI
                buildCustomFontDropdowns();

                // Initialize custom font search
                initializeFontSearch();
            });
    }

    // Function to build custom font dropdowns with search inside
    function buildCustomFontDropdowns() {
        $('.font-dropdown-container').each(function () {
            var targetId = $(this).data('target');
            var $select = $('#' + targetId);
            var $optionsList = $(this).find('.font-options-list');
            var $trigger = $(this).find('.font-dropdown-trigger');
            var selectedVal = $select.val();
            var selectedText = $select.find('option:selected').text();

            // Store original fonts in data attribute for fast searching
            var fontsList = [];
            $select.find('option').each(function () {
                fontsList.push({
                    value: $(this).val(),
                    text: $(this).text()
                });
            });

            // Store fonts data on the container
            $optionsList.data('originalFonts', fontsList);

            // Populate options list
            $optionsList.empty();
            var fragment = document.createDocumentFragment();

            fontsList.forEach(function (font) {
                var $option = $('<div></div>')
                    .addClass('font-option')
                    .attr('data-value', font.value)
                    .text(font.text)
                    .css({
                        'padding': '0.5rem 0.75rem',
                        'cursor': 'pointer',
                        'user-select': 'none'
                    });

                if (font.value === selectedVal) {
                    $option.addClass('font-option-selected');
                }

                fragment.appendChild($option[0]);
            });

            $optionsList[0].appendChild(fragment);

            // Update selected text in trigger
            if (selectedText && selectedVal) {
                $(this).find('.font-selected-text').text(selectedText);
                updateFontPreview(targetId, selectedVal);
            }
        });
    }

    // Function to update font preview
    function updateFontPreview(targetId, fontName) {
        if (!fontName) {
            $('#' + targetId + '_preview').hide();
            return;
        }

        console.log('Updating preview for:', targetId, fontName);

        // Load font from Google Fonts
        var fontLink = 'https://fonts.googleapis.com/css2?family=' + fontName.replace(/ /g, '+') + '&display=swap';
        if (!$('link[href="' + fontLink + '"]').length) {
            $('head').append('<link rel="stylesheet" href="' + fontLink + '">');
        }

        var $preview = $('#' + targetId + '_preview');
        if ($preview.length) {
            $preview.css({
                'font-family': "'" + fontName + "', sans-serif",
                'display': 'flex'
            });
        }
    }

    // Debounce utility function to prevent excessive function calls
    function debounce(func, delay) {
        var timeoutId;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () {
                func.apply(context, args);
            }, delay);
        };
    }

    // Function to perform font search with ultra-optimized filtering
    function performFontSearch($container) {
        var $searchBox = $container.find('.font-search-box');
        var $optionsList = $container.find('.font-options-list');
        var searchValue = $searchBox.val().toLowerCase().trim();
        var originalFonts = $optionsList.data('originalFonts') || [];

        // Get all option elements once
        var allOptions = $optionsList.find('.font-option');
        
        if (searchValue === '') {
            // Remove hidden class from all
            allOptions.removeClass('font-option-hidden');
        } else {
            // Create a set of matching font values for O(1) lookup
            var matchingValues = {};
            originalFonts.forEach(function (font) {
                if (font.text.toLowerCase().includes(searchValue)) {
                    matchingValues[font.value] = true;
                }
            });

            // Update all options at once
            allOptions.each(function () {
                var value = this.getAttribute('data-value');
                if (matchingValues[value]) {
                    this.classList.remove('font-option-hidden');
                } else {
                    this.classList.add('font-option-hidden');
                }
            });
        }

        // Scroll to top when searching
        $optionsList.scrollTop(0);
    }

    // Function to initialize custom font search
    function initializeFontSearch() {
        var debouncedSearchFunctions = {};

        // Toggle dropdown visibility
        $(document).on('click', '.font-dropdown-trigger', function (e) {
            e.stopPropagation();
            var $container = $(this).closest('.font-dropdown-container');
            var $menu = $container.find('.font-dropdown-menu');
            var $optionsList = $container.find('.font-options-list');

            // Close other dropdowns
            $('.font-dropdown-menu').not($menu).removeClass('show');
            
            $menu.toggleClass('show');
            
            if ($menu.hasClass('show')) {
                var $searchBox = $menu.find('.font-search-box');
                $searchBox.focus();
                // Clear previous search when opening
                $searchBox.val('');
                // Remove all hidden classes
                $optionsList.find('.font-option').removeClass('font-option-hidden');
                $optionsList.scrollTop(0);
            }
        });

        // Search functionality with optimized debouncing
        $(document).on('keyup input', '.font-search-box', function (e) {
            var $container = $(this).closest('.font-dropdown-container');
            var containerId = $container.data('target');

            // Create unique debounced function for each container if not exists
            if (!debouncedSearchFunctions[containerId]) {
                debouncedSearchFunctions[containerId] = debounce(function () {
                    performFontSearch($container);
                }, 100); // Reduced to 100ms - super responsive
            }

            // Call the debounced function
            debouncedSearchFunctions[containerId].call(this);
        });

        // Option selection
        $(document).on('click', '.font-option:not(.font-option-hidden)', function (e) {
            e.stopPropagation();
            var $container = $(this).closest('.font-dropdown-container');
            var targetId = $container.data('target');
            var $select = $('#' + targetId);
            var value = this.getAttribute('data-value');
            var text = this.textContent;

            // Update select element
            $select.val(value);

            // Update trigger text
            $container.find('.font-selected-text').text(text);
            
            // Update preview
            updateFontPreview(targetId, value);

            // Update option selection style
            requestAnimationFrame(function () {
                var allOptions = $container.find('.font-option');
                allOptions.forEach(function (opt) {
                    opt.classList.remove('font-option-selected');
                });
                $(this).addClass('font-option-selected');
            }.bind(this));

            // Clear search
            $container.find('.font-search-box').val('');
            $container.find('.font-options-list').find('.font-option').removeClass('font-option-hidden');

            // Close dropdown
            $container.find('.font-dropdown-menu').removeClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.font-dropdown-container').length) {
                $('.font-dropdown-menu').removeClass('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        $(document).on('click', '.font-dropdown-menu', function (e) {
            e.stopPropagation();
        });

        // Handle enter key to select first visible option (for better UX)
        $(document).on('keydown', '.font-search-box', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var $container = $(this).closest('.font-dropdown-container');
                var $firstVisible = $container.find('.font-option:not(.font-option-hidden)').first();
                if ($firstVisible.length) {
                    $firstVisible.click();
                }
            }
        });
    }

    /// START :: ACTIVE MENU CODE
    $(".menu a").each(function () {
        let pageUrl = window.location.href.split(/[?#]/)[0];
        if (this.href == pageUrl) {
            $(this).parent().parent().addClass("active");
            $(this).parent().addClass("active"); // add active to li of the current link
            $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().parent().addClass("active"); // add active class to an anchor
        }

        let subURL = $("a#subURL").attr("href");
        if (subURL != 'undefined') {
            if (this.href == subURL) {
                $(this).parent().addClass("active"); // add active to li of the current link
                $(this).parent().parent().addClass("active");
                $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("active"); // add active class to an anchor
            }
        }
    });
    /// END :: ACTIVE MENU CODE


    FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType);

    if ($('.filepond').length > 0) {
        $('.filepond').filepond({
            credits: null,
            allowFileSizeValidation: "true",
            maxFileSize: '120MB',
            labelMaxFileSizeExceeded: 'File is too large',
            labelMaxFileSize: 'Maximum file size is {filesize}',
            allowFileTypeValidation: true,
            acceptedFileTypes: ['image/*'],
            labelFileTypeNotAllowed: 'File of invalid type',
            fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}',
            storeAsFile: true,
            allowPdfPreview: true,
            pdfPreviewHeight: 320,
            pdfComponentExtraParams: 'toolbar=0&navpanes=0&scrollbar=0&view=fitH',
            allowVideoPreview: true, // default true
            allowAudioPreview: true // default true
        });
    }

    if ($('.zip-pond').length > 0) {
        $('.zip-pond').filepond({
            credits: null,
            allowFileSizeValidation: "true",
            maxFileSize: '120MB',
            labelMaxFileSizeExceeded: 'File is too large',
            labelMaxFileSize: 'Maximum file size is {filesize}',
            allowFileTypeValidation: false,
            acceptedFileTypes: ["zip", "application/octet-stream", "application/zip", "application/x-zip", "application/x-zip-compressed", "zipx", "z01", "zx01"],
            labelFileTypeNotAllowed: 'File of invalid type',
            fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}',
            storeAsFile: true,
            allowPdfPreview: true,
            pdfPreviewHeight: 320,
            pdfComponentExtraParams: 'toolbar=0&navpanes=0&scrollbar=0&view=fitH',
            allowVideoPreview: true, // default true
            allowAudioPreview: true // default true
        });
    }


    //magnific popup
    $(document).on('click', '.image-popup-no-margins', function () {
        $(this).magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            closeBtnInside: false,
            fixedContentPos: true,
            image: {
                verticalFit: true
            },
            zoom: {
                enabled: true,
                duration: 300 // don't forget to change the duration also in CSS
            },
            gallery: {
                enabled: true
            },
        }).magnificPopup('open');
        return false;
    });

    $('#table_list').on('load-success.bs.table', function () {
        if ($('.gallery').length > 0) {
            $('.gallery').each(function () { // the containers for all your galleries
                $(this).magnificPopup({
                    delegate: 'a', // the selector for gallery item
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                });
            });
        }
    })

    $(document).off('focusin');
});


/// START :: TinyMCE
document.addEventListener("DOMContentLoaded", () => {
    tinymce.init({
        selector: '#tinymce_editor',
        height: 400,
        menubar: true,
        plugins: [
            'advlist autolink lists link charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime table paste code help wordcount'
        ],

        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        setup: function (editor) {
            editor.on("change keyup", function () {
                //tinyMCE.triggerSave(); // updates all instances
                editor.save(); // updates this instance's textarea
                $(editor.getElement()).trigger('change'); // for garlic to detect change
            });
        }
    });
});

$('body').append('<div id="loader-container"><div class="loader"></div></div>');
$(window).on('load', function () {
    $('#loader-container').fadeOut('slow');
});

setTimeout(function () {
    $(".error-msg").fadeOut(1500)
}, 5000);

document.addEventListener('touchstart', event => {
    if (event.cancelable) {
        event.preventDefault();
    }
});

document.addEventListener('touchmove', event => {
    if (event.cancelable) {
        event.preventDefault();
    }
});

document.addEventListener('touchcancel', event => {
    if (event.cancelable) {
        event.preventDefault();
    }
});

$('.status-switch').on('change', function () {
    if ($(this).is(":checked")) {
        $(this).siblings('input[type="hidden"]').val(1);
    } else {
        $(this).siblings('input[type="hidden"]').val(0);
    }
})

$('input[type="radio"][name="duration_type"]').on('click', function () {
    if ($(this).hasClass('edit_duration_type')) {
        if ($(this).is(':checked')) {
            if ($(this).val() == 'limited') {
                $('#edit_limitation_for_duration').show();
                $('#edit_durationLimit').attr("required", "true").val("");
            } else {
                // Unlimited
                $('#edit_limitation_for_duration').hide();
                $('#edit_durationLimit').removeAttr("required").val("");
            }
        }
    } else {
        if ($(this).is(':checked')) {
            if ($(this).val() == 'limited') {
                $('#limitation_for_duration').show();
                $('#durationLimit').attr("required", "true").val("");
            } else {
                // Unlimited
                $('#limitation_for_duration').hide();
                $('#durationLimit').removeAttr("required").val("");
            }
        }
    }
});

$('input[type="radio"][name="item_limit_type"]').on('click', function () {
    if ($(this).hasClass('edit_item_limit_type')) {
        if ($(this).is(':checked')) {
            if ($(this).val() == 'limited') {
                $('#edit_limitation_for_limit').show();
                $('#edit_ForLimit').attr("required", "true");
            } else {
                // Unlimited
                $('#edit_limitation_for_limit').hide();
                $('#edit_ForLimit').val('');
                $('#edit_ForLimit').removeAttr("required");
            }
        }
    } else {
        if ($(this).is(':checked')) {
            if ($(this).val() == 'limited') {
                $('#limitation_for_limit').show();
                $('#durationForLimit').attr("required", "true");
            } else {
                // Unlimited
                $('#limitation_for_limit').hide();
                $('#durationForLimit').removeAttr("required");
            }
        }
    }
});

$('#filter').change(function () {
    let selectedValue = $(this).val();
    // Hide all criteria elements initially
    $('#category_criteria, #price_criteria').hide();
    // Show the relevant criteria based on the selected option
    if (selectedValue === "category_criteria") {
        $('#category_criteria').show();
    } else if (selectedValue === "price_criteria") {
        $('#price_criteria').show();
    }
});

$('#user_notification_list').on('check.bs.table  uncheck.bs.table', function () {
    let fcm_list = [];
    let user_list = [];
    let data = $("#user_notification_list").bootstrapTable('getSelections');
    data.forEach(function (value) {
        if (value.fcm_id != "") {
            fcm_list.push(value.fcm_id);
        }
        if (value.id != "") {
            user_list.push(value.id);
        }
    })

    $('textarea#fcm_id').text(fcm_list);
    $('textarea#user_id').text(user_list);
});

$('#delete_multiple').on('click', function (e) {
    e.preventDefault();
    let table = $('#table_list');
    let selected = table.bootstrapTable('getSelections');
    let ids = "";

    $.each(selected, function (i, e) {
        ids += e.id + ",";
    });
    ids = ids.slice(0, -1);
    if (ids == "") {
        showErrorToast(trans('Please Select Notification First'));
    } else {
        showDeletePopupModal($(this).attr('href'), {
            data: {
                id: ids
            }, successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }
});


$(".checkbox-toggle-switch").on('change', function () {
    let inputValue = $(this).is(':checked') ? 1 : 0;
    $(this).siblings(".checkbox-toggle-switch-input").val(inputValue);
});

$('.toggle-button').on('click', function (e) {
    e.preventDefault();
    $(this).closest('.category-header').next('.subcategories').slideToggle();
});

let length = $('#sub_category_count').val();

for (let i = 1; i <= length; i++) {
    $('.child_category_list' + i).hide();
    $('#sub_category' + i).change(function () {
        $('#child_category' + i).prop("checked", $(this).is(":checked"));
    });

    $('#category_arrow' + i).on('click', function () {
        $('.child_category_list' + i).toggle();
    });
}

$('#type').on('change', function () {
    if ($.inArray($(this).val(), ['checkbox', 'radio', 'dropdown']) > -1) {
        $('#field-values-div').slideDown(500);
        $('.min-max-fields').slideUp(500);
    } else {
        $('#field-values-div').slideUp(500);
        $('.min-max-fields').slideDown(500);
    }
});

$('.image').on('change', function () {
    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    const fileInput = this;
    const [file] = fileInput.files;
    if (!file) {
        return; // No file selected
    }

    if (!allowedExtensions.exec(file.name)) {
        $('.img_error').text('Invalid file type. Please choose an image file.');
        fileInput.value = '';
        return;
    }

    const maxFileSize = 2 * 1024 * 1024; // 5MB (adjust as needed)
    if (file.size > maxFileSize) {
        $('.img_error').text('File size exceeds the maximum allowed size (2MB).');
        fileInput.value = '';
    }
    if (file) {
        $(this).siblings('.preview-image').attr('src', URL.createObjectURL(file))
    }
});

$(".toggle-password").on('click', function () {
    $(this).toggleClass("bi bi-eye bi-eye-slash");
    let input = $(this).parent().siblings("input");
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$('#price,#discount_in_percentage').on('input', function () {
    let price = $('#price').val();
    let discount = $('#discount_in_percentage').val();
    let final_price = calculateDiscountedAmount(price, discount);
    $('#final_price').val(final_price);
})

$('#final_price').on('input', function () {
    let discountedPrice = $(this).val();
    let price = $('#price').val();
    let discount = calculateDiscount(price, discountedPrice);
    $('#discount_in_percentage').val(discount);
})


$('#edit_price,#edit_discount_in_percentage').on('input', function () {
    let price = $('#edit_price').val();
    let discount = $('#edit_discount_in_percentage').val();
    let final_price = calculateDiscountedAmount(price, discount);
    $('#edit_final_price').val(final_price);
})

$('#edit_final_price').on('input', function () {
    let discountedPrice = $(this).val();
    let price = $('#edit_price').val();
    let discount = calculateDiscount(price, discountedPrice);
    $('#edit_discount_in_percentage').val(discount);
})
$('#slug').bind('keyup blur', function () {
    $(this).val($(this).val().replace(/[^A-Za-z0-9-]/g, ''))
});

function toggleRejectedReasonVisibility() {
    var status = $('#status').val();
    var rejectedReasonContainer = $('#rejected_reason_container');
    if (status === 'rejected') {
        rejectedReasonContainer.show();
    } else {
        rejectedReasonContainer.hide();
    }
}

$('.editdata, #status').on('click change', function () {
    toggleRejectedReasonVisibility();
});

$(document).on('change', '.update-item-status', function () {
    let url = window.baseurl + "common/change-status";
    ajaxRequest('PUT', url, {
        id: $(this).attr('id'),
        table: "items",
        column: "deleted_at",
        status: $(this).is(':checked') ? 1 : 0
    }, null, function (response) {
        showSuccessToast(response.message);
    }, function (error) {
        showErrorToast(error.message);
    })
})

$(document).on('change', '.update-user-status', function () {
    let url = window.baseurl + "common/change-status";
    ajaxRequest('PUT', url, {
        id: $(this).attr('id'),
        table: "users",
        column: "deleted_at",
        status: $(this).is(':checked') ? 1 : 0
    }, null, function (response) {
        showSuccessToast(response.message);
    }, function (error) {
        showErrorToast(error.message);
    })
})

$('#switch_banner_ad_status').on('change', function () {
    $('#banner_ad_id_android').attr('required', $(this).is(':checked'));
    $('#banner_ad_id_ios').attr('required', $(this).is(':checked'));
})

$('.package_type').on('change', function () {
    if ($(this).val() == 'item_listing') {
        $('#item-listing-package-div').show();
        $('#advertisement-package-div').hide();

        $('#item-listing-package').attr('required', true);
        $('#advertisement-package').attr('required', false);
    } else if ($(this).val() == 'advertisement') {
        $('#item-listing-package-div').hide();
        $('#advertisement-package-div').show();

        $('#advertisement-package').attr('required', true);
        $('#item-listing-package').attr('required', false);
    }
});

$('.package').on('change', function () {
    let package_detail = $(this).find('option:selected').data('details');
    if (package_detail != null) {
        $('#package_details').show();
        $('.payment').show();
    } else {
        $('#package_details').hide();
        $('.payment').hide();
        $('.cheque').hide();
    }
    $("#package_name").text(package_detail?.name);
    $("#package_price").text(package_detail?.price);
    $("#package_final_price").text(package_detail?.final_price);
    $("#package_duration").text(package_detail?.duration);
});
$('.payment_gateway').change(function () {
    if ($(this).val() == 'cheque') {
        $('.cheque').show();
    } else {
        $('.cheque').hide();
    }

    $('.payment').val('').trigger('change');
});


$('#switch_interstitial_ad_status').on('change', function () {
    $('#interstitial_ad_id_android').attr('required', $(this).is(':checked'));
    $('#interstitial_ad_id_ios').attr('required', $(this).is(':checked'));
})

$('#country').on('change', function () {
    let countryId = $(this).val();
    let url = window.baseurl + 'states/search?country_id=' + countryId;
    ajaxRequest('GET', url, null, null, function (response) {
        $('#state').html("<option value=''>" + window.trans("--Select State--") + "</option>")
        $.each(response.data, function (key, value) {
            $('#state').append($('<option>', {
                value: value.id,
                text: value.name
            }));
        });
    })
});

$('#state').on('change', function () {
    let stateId = $(this).val();
    let url = window.baseurl + 'cities/search?state_id=' + stateId;
    ajaxRequest('GET', url, null, null, function (response) {
        $('#city').html("<option value=''>" + window.trans("--Select City--") + "</option>")
        $.each(response.data, function (key, value) {
            $('#city').append($('<option>', {
                value: value.id,
                text: value.name
            }));
        });
    })
});

$('#filter_country').on('change', function () {
    let countryId = $(this).val();
    let url = window.baseurl + 'states/search?country_id=' + countryId;
    ajaxRequest('GET', url, null, null, function (response) {
        $('#filter_state').html("<option value=''>" + window.trans("All") + "</option>")
        $.each(response.data, function (key, value) {
            $('#filter_state').append($('<option>', {
                value: value.id,
                text: value.name
            }));
        });
    })
});

$('#filter_state').on('change', function () {
    let stateId = $(this).val();
    let url = window.baseurl + 'cities/search?state_id=' + stateId;
    ajaxRequest('GET', url, null, null, function (response) {
        $('#filter_city').html("<option value=''>" + window.trans("All") + "</option>")
        $.each(response.data, function (key, value) {
            $('#filter_city').append($('<option>', {
                value: value.id,
                text: value.name
            }));
        });
    })
});

$(document).ready(function () {
    const $addAreaButton = $('#add-area-button');
    const $areasContainer = $('#areas-container');

    $addAreaButton.on('click', function () {
        const $newAreaInputGroup = $(`
            <div class="area-input-group col-md-12">
                <label for="name" class="mandatory form-label mt-2"> Area Name</label>
                <div class="d-flex">
                    <input type="text" name="name[]" class="form-control me-2" placeholder="Enter Area name">
                    <button type="button" class="btn btn-danger remove-area-button ">-</button>
                </div>
            </div>
        `);
        $areasContainer.append($newAreaInputGroup);
    });

    // Event delegation to handle dynamically added remove buttons
    $areasContainer.on('click', '.remove-area-button', function () {
        $(this).closest('.area-input-group').remove();
    });
});

$('#switch_stripe_gateway').on('change', function () {
    let status = $(this).prop('checked');
    $('[name^="gateway[Stripe]"]').each(function () {
        $(this).prop('required', status);
    });
});

$('#switch_razorpay_gateway').on('change', function () {
    let status = $(this).prop('checked');
    $('[name^="gateway[Razorpay]"]').each(function () {
        $(this).prop('required', status);
    });
});

$('#switch_paystack_gateway').on('change', function () {
    let status = $(this).prop('checked');
    $('[name^="gateway[Paystack]"]').each(function () {
        $(this).prop('required', status);
    });
});

$('#google_map_iframe_link').on('input', function () {
    try {
        let element = $(this).val();
        let src = $(element).attr('src');
        $(this).val(src);
    } catch (err) {
        $(this).val("");
        showErrorToast("Please enter a valid map iframe")
    }

});
$('#category_name').on('input', function () {
    let name = $(this).val();
    let slug = name.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]+/g, '');
    $('#category_slug').val(slug);
});

$(document).ready(function () {
    /* Form Validation for Privacy policy */
    $('#submit_button').on('click', function (event) {
        event.preventDefault();

        // Find the TinyMCE editor in Privacy policy form
        var privacyEditor = null;
        tinymce.editors.forEach(function (editor) {
            if ($(editor.getElement()).closest('#Privacy_and_policy').length > 0) {
                privacyEditor = editor;
            }
        });

        var privacy_policy = '';
        if (privacyEditor) {
            privacy_policy = privacyEditor.getContent();
            // Update textarea with TinyMCE content
            $(privacyEditor.getElement()).val(privacy_policy);
        } else {
            // Fallback to textarea value
            privacy_policy = $('#Privacy_and_policy #tinymce_editor').val();
        }

        if (privacy_policy.trim() === "" || privacy_policy === "<p><br></p>" || privacy_policy === "<p></p>") {
            $('#privacy_policy_error').removeClass('d-none');
            return false;
        } else {
            $('#privacy_policy_error').addClass('d-none');
            $('#Privacy_and_policy').submit();
        }
    });

    /* Form Validation for Terms and conditions */
    $('#terms_and_conditions_submit').on('click', function (event) {
        event.preventDefault();

        // Find the TinyMCE editor in Terms and conditions form
        var termsEditor = null;
        tinymce.editors.forEach(function (editor) {
            if ($(editor.getElement()).closest('#terms_and_conditions_form').length > 0) {
                termsEditor = editor;
            }
        });

        var terms_and_conditions = '';
        if (termsEditor) {
            terms_and_conditions = termsEditor.getContent();
            // Update textarea with TinyMCE content
            $(termsEditor.getElement()).val(terms_and_conditions);
        } else {
            // Fallback to textarea value
            terms_and_conditions = $('#terms_and_conditions_form #tinymce_editor').val();
        }

        if (terms_and_conditions.trim() === "" || terms_and_conditions === "<p><br></p>" || terms_and_conditions === "<p></p>") {
            $('#terms_and_conditions_error').removeClass('d-none');
            return false;
        } else {
            $('#terms_and_conditions_error').addClass('d-none');
            $('#terms_and_conditions_form').submit();
        }
    });

    /* Form validation for About Us */
    $('#about_us_submit').on('click', function (event) {
        event.preventDefault();

        // Find the TinyMCE editor in About Us form
        var aboutEditor = null;
        tinymce.editors.forEach(function (editor) {
            if ($(editor.getElement()).closest('#about_us_form').length > 0) {
                aboutEditor = editor;
            }
        });

        var about_us = '';
        if (aboutEditor) {
            about_us = aboutEditor.getContent();
            // Update textarea with TinyMCE content
            $(aboutEditor.getElement()).val(about_us);
        } else {
            // Fallback to textarea value
            about_us = $('#about_us_form #tinymce_editor').val();
        }

        if (about_us.trim() === "" || about_us === "<p><br></p>" || about_us === "<p></p>") {
            $('#about_us_error').removeClass('d-none');
            return false;
        } else {
            $('#about_us_error').addClass('d-none');
            $('#about_us_form').submit();
        }
    });
});

// Keep your existing TinyMCE initialization exactly as it was:
var mode = $("body").attr('data-bs-theme');

if (mode == "dark") {
    tinymce.init({
        selector: "#tinymce_editor",
        skin: window.matchMedia("(prefers-color-scheme: dark)").matches ? "oxide-dark" : "oxide",
        content_css: window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "default",
        height: 400
    });
}

/* Rss Feed Models*/
$(document).ready(function () {
    $('#channels_id').select2()
})

$(document).ready(function () {
    $('#topics_id').select2()
})


// //  <><><><><><><> ADD JS FOR DRAG AND DROP REORDERING NEWS LANGUAGES <><><><><><><><><><>
// $(document).ready(function () {
//     // Set up CSRF token for all AJAX requests
//     $.ajaxSetup({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

//         }
//     });

//     // Enable drag-and-drop sorting
//     $("#sortable tbody").sortable({
//         cursor: "move",
//         placeholder: "sortable-placeholder",
//         helper: function (e, tr) {
//             var $originals = tr.children();
//             var $helper = tr.clone();
//             $helper.children().each(function (index) {
//                 $(this).width($originals.eq(index).width());
//             });
//             return $helper;
//         },
//         stop: function (event, ui) {
//             updatePosition(); // Update position after drag-and-drop
//         }
//     }).disableSelection();

//     // Handle input change in position number field
//     $(document).on('change', '.reorder-input', function () {
//         let rows = [];

//         // Collect rows and their position values
//         $('#sortable tbody tr').each(function () {
//             const positionVal = parseInt($(this).find('.reorder-input').val()) || 9999;
//             const rowId = $(this).data('id');
//             rows.push({
//                 id: rowId,
//                 position: positionVal,
//                 html: $(this).prop('outerHTML')
//             });
//         });

//         // Sort rows by position number
//         rows.sort((a, b) => a.position - b.position);

//         // Update table and renumber inputs
//         const $tbody = $('#sortable tbody');
//         $tbody.html('');
//         rows.forEach((row, index) => {
//             const $row = $(row.html);
//             $row.find('.reorder-input').val(index + 1);
//             $tbody.append($row);
//         });

//         // Save new position to server
//         updatePosition();
//     });

//     // Function to update position on server
//     function updatePosition() {
//         let positionData = [];
//         $('#sortable tbody tr').each(function (index) {
//             const id = $(this).data('id');
//             const position = index + 1;
//             $(this).find('.reorder-input').val(position); // Ensure input reflects position
//             positionData.push({
//                 id: id,
//                 position: position
//             });
//         });

//         $.ajax({

//             url: window.baseurl + 'admin/news-languages/reorder',
//             method: 'POST',
//             data: {
//                 order: positionData
//             },
//             success: function (response) {
//                 if (response.success) {
//                     // Find the index of the DEFAULT_LANGUAGE column
//                     let badgeColumnIndex = -1;
//                     $('#sortable thead th').each(function (index) {
//                         if ($(this).text().trim() === 'DEFAULT_LANGUAGE') {
//                             badgeColumnIndex = index;
//                             return false; // Break loop
//                         }
//                     });

//                     // Update the badge for each row
//                     if (badgeColumnIndex !== -1) {
//                         $('#sortable tbody tr').each(function (index) {
//                             const $badgeCell = $(this).find('td').eq(badgeColumnIndex);
//                             if (index === 0) {
//                                 $badgeCell.html('<span class="badge bg-success text-white">YES</span>');
//                             } else {
//                                 $badgeCell.html('<span class="badge bg-danger text-white">NO</span>');
//                             }
//                         });
//                     }

//                     Swal.fire({
//                         icon: 'success',
//                         title: 'Success',
//                         text: response.message,
//                         confirmButtonText: 'OK',
//                         allowOutsideClick: false, // prevent closing on outside click
//                         allowEscapeKey: false,    // prevent closing on ESC
//                         allowEnterKey: true       // allow closing on pressing Enter
//                     }).then((result) => {
//                         if (result.isConfirmed) {
//                             location.reload(); // Reload the page after clicking OK
//                         }
//                     });

//                 } else {
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Error',
//                         text: response.message
//                     });
//                 }
//             }

//         });
//     }
// });
// //  <><><><><><><> END JS OF DRAG AND DROP REORDERING NEWS LANGUAGES <><><><><><><>

//  <><><><><><><> ADD JS FOR DRAG AND DROP REORDERING NEWS LANGUAGES <><><><><><><><><><>
$(document).ready(function () {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ✅ Get permission from data attribute in table
    let canReorder = $("#sortable tbody tr td.default-lang-cell").first().data("can-reorder") == "1";

    if (canReorder) {
        $("#sortable tbody").sortable({
            cursor: "move",
            placeholder: "sortable-placeholder",
            helper: function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            stop: function () {
                updatePosition();
            }
        }).disableSelection();
    } else {
        $("#sortable tbody").sortable({
            cancel: "tr"
        });

        $("#sortable tbody").on("mousedown", "tr", function (e) {
            if (
                $(e.target).is("a, button, input, select, textarea, i, svg, span") ||
                $(e.target).closest("a, button").length
            ) {
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'No Permission',
                text: 'You do not have permission to reorder or set default language.',
                confirmButtonText: 'OK'
            });
            e.preventDefault();
        });
    }

    // Handle manual input change in position number field
    $(document).on('change', '.reorder-input', function () {
        if (!canReorder) {
            Swal.fire({
                icon: 'info',
                title: 'No Permission',
                text: 'You do not have permission to reorder or set default language.',
                confirmButtonText: 'OK'
            });
            // Reset value back to original sequence
            let index = $(this).closest("tr").index() + 1;
            $(this).val(index);
            return;
        }

        let rows = [];

        // Collect rows and their position values
        $('#sortable tbody tr').each(function () {
            const positionVal = parseInt($(this).find('.reorder-input').val()) || 9999;
            const rowId = $(this).data('id');
            rows.push({
                id: rowId,
                position: positionVal,
                element: $(this).detach() // detach keeps events & DOM intact
            });
        });

        // Sort rows by entered position number
        rows.sort((a, b) => a.position - b.position);

        // Re-append sorted rows
        const $tbody = $('#sortable tbody');
        rows.forEach((row, index) => {
            row.element.find('.reorder-input').val(index + 1);
            $tbody.append(row.element);
        });

        // Save new position to server
        updatePosition();
    });

    // Function to update position on server
    function updatePosition() {
        let positionData = [];
        $('#sortable tbody tr').each(function (index) {
            const id = $(this).data('id');
            const position = index + 1;
            $(this).find('.reorder-input').val(position); // Ensure input reflects position
            positionData.push({ id: id, position: position });
        });

        $.ajax({
            url: window.baseurl + 'admin/news-languages/reorder',
            method: 'POST',
            data: { order: positionData },
            success: function (response) {
                if (response.success) {
                    // Find the index of the DEFAULT_LANGUAGE column
                    let badgeColumnIndex = -1;
                    $('#sortable thead th').each(function (index) {
                        if ($(this).text().trim() === 'DEFAULT_LANGUAGE') {
                            badgeColumnIndex = index;
                            return false;
                        }
                    });

                    // Update the badge for each row
                    if (badgeColumnIndex !== -1) {
                        $('#sortable tbody tr').each(function (index) {
                            const $badgeCell = $(this).find('td').eq(badgeColumnIndex);
                            if (index === 0) {
                                $badgeCell.html('<span class="badge bg-success text-white">YES</span>');
                            } else {
                                $badgeCell.html('<span class="badge bg-danger text-white">NO</span>');
                            }
                        });
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            }
        });
    }
});
//  <><><><><><><> END JS OF DRAG AND DROP REORDERING NEWS LANGUAGES <><><><><><><><><><>

//  <><><><><><><> START JS FOR CHANGE THEME <><><><><><><>
document.addEventListener("DOMContentLoaded", function () {
    var themeConfig = {
        theme: "light",
        "theme-base": "gray",
        "theme-font": "sans-serif",
        "theme-primary": "blue",
        "theme-radius": "1",
    };

    var url = new URL(window.location);
    var form = document.getElementById("offcanvasSettings");
    var resetButton = document.getElementById("reset-changes");
    var saveButton = form.querySelector('a.btn-primary'); // Save settings button

    // Apply saved theme from localStorage or defaults to document.documentElement
    function applyThemeFromStorage() {
        for (var key in themeConfig) {
            var value = window.localStorage.getItem("tabler-" + key) || themeConfig[key];
            if (value) {
                document.documentElement.setAttribute("data-bs-" + key, value);
            }
        }
    }

    // Update radio inputs to match saved theme or defaults
    function checkItems() {
        for (var key in themeConfig) {
            var value = window.localStorage.getItem("tabler-" + key) || themeConfig[key];
            if (value) {
                var radios = form.querySelectorAll(`[name="${key}"]`);
                radios.forEach((radio) => {
                    radio.checked = radio.value === value;
                });
            }
        }
    }

    // When "Save settings" clicked, save selected values and apply them
    saveButton.addEventListener("click", function (event) {
        event.preventDefault();

        var formData = new FormData(form);
        for (var key in themeConfig) {
            var value = formData.get(key) || themeConfig[key];
            window.localStorage.setItem("tabler-" + key, value);
            document.documentElement.setAttribute("data-bs-" + key, value);
            url.searchParams.set(key, value);
        }
        window.history.pushState({}, "", url);

        // Close offcanvas manually (Bootstrap 5 way)
        var offcanvas = bootstrap.Offcanvas.getInstance(form);
        if (offcanvas) {
            offcanvas.hide();
        }
    });

    // Reset changes: clear localStorage, reset radios, apply default theme immediately
    resetButton.addEventListener("click", function () {
        for (var key in themeConfig) {
            window.localStorage.removeItem("tabler-" + key);
            document.documentElement.removeAttribute("data-bs-" + key);
            url.searchParams.delete(key);
        }
        checkItems();
        applyThemeFromStorage();
        window.history.pushState({}, "", url);
    });

    // On page load: apply saved theme and set form inputs accordingly
    applyThemeFromStorage();
    checkItems();
});
//  <><><><><><><> START JS FOR CHANGE THEME <><><><><><><>

//  <><><><><><><> START JS FOR FREE TRIAL STATUS SETTINGS <><><><><><><>
// document.addEventListener('DOMContentLoaded', function () {
//     const freeTrialSwitch = document.getElementById('switch_free_trial_status');
//     const freeTrialStatusInput = document.getElementById('free_trial_status');
//     const postLimitInput = document.getElementById('free_trial_post_limit');
//     const storyLimitInput = document.getElementById('free_trial_story_limit');
//     const epaperLimitInput = document.getElementById('free_trial_e_papers_and_magazines_limit');

//     function updateLimitInputs() {
//         if (freeTrialSwitch.checked) {
//             freeTrialStatusInput.value = 1;
//             postLimitInput.value = -1;
//             storyLimitInput.value = -1;
//             epaperLimitInput.value = -1;

//             postLimitInput.setAttribute('readonly', 'readonly');
//             storyLimitInput.setAttribute('readonly', 'readonly');
//             epaperLimitInput.setAttribute('readonly', 'readonly');
//         } else {
//             freeTrialStatusInput.value = 0;
//             postLimitInput.removeAttribute('readonly');
//             storyLimitInput.removeAttribute('readonly');
//             epaperLimitInput.removeAttribute('readonly');
//             // Optionally reset to default values or leave as is
//             if (postLimitInput.value === '-1') postLimitInput.value = '';
//             if (storyLimitInput.value === '-1') storyLimitInput.value = '';
//             if (epaperLimitInput.value === '-1') epaperLimitInput.value = '';
//         }
//     }

//     // Initial check to set the correct state on page load
//     updateLimitInputs();

//     // Add event listener for changes to the switch
//     freeTrialSwitch.addEventListener('change', updateLimitInputs);
// });
document.addEventListener('DOMContentLoaded', function () {
    const freeTrialSwitch = document.getElementById('switch_free_trial_status');
    const freeTrialStatusInput = document.getElementById('free_trial_status');
    const postLimitInput = document.getElementById('free_trial_post_limit');
    const storyLimitInput = document.getElementById('free_trial_story_limit');
    const epaperLimitInput = document.getElementById('free_trial_e_papers_and_magazines_limit');

    // Check if all required elements exist
    if (!freeTrialSwitch || !freeTrialStatusInput || !postLimitInput || !storyLimitInput || !epaperLimitInput) {
        return; // Exit early to avoid errors
    }

    function updateLimitInputs() {
        if (freeTrialSwitch.checked) {
            freeTrialStatusInput.value = 1;
            postLimitInput.value = -1;
            storyLimitInput.value = -1;
            epaperLimitInput.value = -1;

            postLimitInput.setAttribute('readonly', 'readonly');
            storyLimitInput.setAttribute('readonly', 'readonly');
            epaperLimitInput.setAttribute('readonly', 'readonly');
        } else {
            freeTrialStatusInput.value = 0;
            postLimitInput.removeAttribute('readonly');
            storyLimitInput.removeAttribute('readonly');
            epaperLimitInput.removeAttribute('readonly');

            if (postLimitInput.value === '-1') postLimitInput.value = '';
            if (storyLimitInput.value === '-1') storyLimitInput.value = '';
            if (epaperLimitInput.value === '-1') epaperLimitInput.value = '';
        }
    }

    updateLimitInputs();
    freeTrialSwitch.addEventListener('change', updateLimitInputs);
});

//  <><><><><><><> END JS OF FREE TRIAL STATUS SETTINGS <><><><><><><>

//  <><><><><><><> START JS FOR FREE TRIAL MODE IN  SETTINGS <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('switch_free_trial_status');
    const hiddenInput = document.getElementById('free_trial_status');

    if (toggle && hiddenInput) {
        const hasActiveSubscription = toggle.dataset.hasActiveSubscription === '1';

        toggle.addEventListener('change', function (e) {
            if (this.checked && hasActiveSubscription) {
                this.checked = false;
                hiddenInput.value = 0;

                Swal.fire({
                    icon: 'error',
                    title: 'Not Allowed',
                    text: 'You cannot activate Free Trial Mode because some users already have active subscriptions.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: true
                }).then(() => {
                    // Redirect to another page after user clicks "OK"
                    window.location.reload(); // change this to your desired URL
                });

                e.preventDefault();
            } else {
                hiddenInput.value = this.checked ? 1 : 0;
            }
        });
    }
});

//  <><><><><><><> END JS OF  FREE TRIAL MODE IN  SETTINGS <><><><><><><>



//  <><><><><><><> START JS FOR SEARCH ON SETTINGS <><><><><><><>
const currencySymbols = {
    'USD': '$',
    'AED': 'د.إ',
    'AFN': '؋',
    'ALL': 'L',
    'AMD': '֏',
    'ANG': 'ƒ',
    'AOA': 'Kz',
    'ARS': '$',
    'AUD': 'A$',
    'AWG': 'ƒ',
    'AZN': '₼',
    'BAM': 'KM',
    'BBD': '$',
    'BDT': '৳',
    'BGN': 'лв',
    'BMD': '$',
    'BND': '$',
    'BOB': 'Bs.',
    'BRL': 'R$',
    'BSD': '$',
    'BWP': 'P',
    'BYN': 'Br',
    'BZD': 'BZ$',
    'CAD': 'C$',
    'CDF': 'FC',
    'CHF': 'Fr',
    'CNY': '¥',
    'COP': '$',
    'CRC': '₡',
    'CVE': '$',
    'CZK': 'Kč',
    'DKK': 'kr',
    'DOP': 'RD$',
    'DZD': 'د.ج',
    'EGP': 'E£',
    'ETB': 'Br',
    'EUR': '€',
    'FJD': '$',
    'FKP': '£',
    'GBP': '£',
    'GEL': '₾',
    'GIP': '£',
    'GMD': 'D',
    'GTQ': 'Q',
    'GYD': '$',
    'HKD': 'HK$',
    'HNL': 'L',
    'HTG': 'G',
    'HUF': 'Ft',
    'IDR': 'Rp',
    'ILS': '₪',
    'INR': '₹',
    'ISK': 'kr',
    'JMD': '$',
    'JPY': '¥',
    'KES': 'KSh',
    'KGS': 'сом',
    'KHR': '៛',
    'KYD': '$',
    'KZT': '₸',
    'LAK': '₭',
    'LBP': 'ل.ل',
    'LKR': 'Rs',
    'LRD': '$',
    'LSL': 'L',
    'MAD': 'د.م.',
    'MDL': 'L',
    'MKD': 'ден',
    'MMK': 'K',
    'MNT': '₮',
    'MOP': 'P',
    'MUR': '₨',
    'MVR': 'ރ',
    'MWK': 'MK',
    'MXN': '$',
    'MYR': 'RM',
    'MZN': 'MT',
    'NAD': '$',
    'NGN': '₦',
    'NIO': 'C$',
    'NOK': 'kr',
    'NPR': '₨',
    'NZD': 'NZ$',
    'PAB': 'B/.',
    'PEN': 'S/',
    'PGK': 'K',
    'PHP': '₱',
    'PKR': '₨',
    'PLN': 'zł',
    'QAR': 'ر.ق',
    'RON': 'lei',
    'RSD': 'дин.',
    'RUB': '₽',
    'SAR': 'ر.س',
    'SBD': '$',
    'SCR': '₨',
    'SEK': 'kr',
    'SGD': 'S$',
    'SHP': '£',
    'SLE': 'Le',
    'SOS': 'S',
    'SRD': '$',
    'STD': 'Db',
    'SZL': 'L',
    'THB': '฿',
    'TJS': 'ЅМ',
    'TOP': 'T$',
    'TRY': '₺',
    'TTD': 'TT$',
    'TWD': 'NT$',
    'TZS': 'TSh',
    'UAH': '₴',
    'UYU': '$U',
    'UZS': "so'm",
    'WST': 'T',
    'XCD': '$',
    'YER': '﷼',
    'ZAR': 'R',
    'ZMW': 'ZK'
};

$(document).ready(function () {
    // Hide all gateway forms initially
    $('.gateway-form').hide();

    // Show the first form by default (optional, remove if you want no form visible initially)
    $('#stripe-form').show();

    // Handle button clicks
    $('button[data-gateway]').on('click', function () {
        // Hide all forms
        $('.gateway-form').hide();
        // Show the selected form
        const gateway = $(this).data('gateway');
        $(`#${gateway}-form`).show();
    });

    // For Stripe
    $('#switch_stripe_gateway').on('change', function () {
        $('#stripe_gateway').val($(this).is(':checked') ? '1' : '0');
    });

    // For Razorpay
    $('#switch_razorpay_gateway').on('change', function () {
        $('#razorpay_gateway').val($(this).is(':checked') ? '1' : '0');
    });
    // For Apple Pau
    $('#switch_applepay_gateway').on('change', function () {
        $('#applepay_gateway').val($(this).is(':checked') ? '1' : '0');
    });

    function updateCurrencySymbol(selectElement, symbolField, hiddenField) {
        const selectedCurrency = $(selectElement).val();
        const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
        $(symbolField).val(symbol);
        $(hiddenField).val(symbol); // Update hidden field
    }

    // Set initial values
    updateCurrencySymbol('#stripe_currency_code', '#stripe_currency_symbol',
        '#stripe_currency_symbol_hidden');


    updateCurrencySymbol('#razorpay_currency_code', '#razorpay_currency_symbol',
        '#razorpay_currency_symbol_hidden');

    updateCurrencySymbol('#applepay_currency_code', '#applepay_currency_symbol',
        '#applepay_currency_symbol_hidden');

    updateCurrencySymbol('#custom_ads_currency_code', '#custom_ads_currency_symbol',
        '#custom_ads_currency_symbol_hidden');
    // Add event listeners for changes
    $('#stripe_currency_code').on('change', function () {
        updateCurrencySymbol('#stripe_currency_code', '#stripe_currency_symbol',
            '#stripe_currency_symbol_hidden');
    });

    $('#razorpay_currency_code').on('change', function () {
        updateCurrencySymbol('#razorpay_currency_code', '#razorpay_currency_symbol',
            '#razorpay_currency_symbol_hidden');
    });

    $('#applepay_currency_code').on('change', function () {
        updateCurrencySymbol('#applepay_currency_code', '#applepay_currency_symbol',
            '#applepay_currency_symbol_hidden');
    });

    $('#custom_ads_currency_code').on('change', function () {
        updateCurrencySymbol('#custom_ads_currency_code', '#custom_ads_currency_symbol',
            '#custom_ads_currency_symbol_hidden');
    });
    // Initialize select2
    $('#stripe_currency_code').val($('#stripe_currency_code').data('currency-code')).trigger('change');
    $('#custom_ads_currency_code').val($('#custom_ads_currency_code').data('currency-code')).trigger('change');
    $('#razorpay_currency_code').val($('#razorpay_currency_code').data('currency-code')).trigger('change');
    $('#applepay_currency_code').val($('#applepay_currency_code').data('currency-code')).trigger('change');
});

//  <><><><><><><> END JS OF SEARCH ON SETTINGS <><><><><><><>

//  <><><><><><><> START JS FOR ROLE EDIT BUTTON<><><><><><><>
$('#edit_role_submit_button').on('click', function (e) {
    const btn = document.getElementById("edit_role_submit_button");
    btn.disabled = false;
    const observer = new MutationObserver(() => {
        if (btn.disabled) {
            btn.disabled = false;
        }
    });
    observer.observe(btn, { attributes: true, attributeFilter: ['disabled'] });
});
//  <><><><><><><> END JS OF ROLE EDIT BUTTON <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
    // Global Select All
    const globalSelectAll = document.getElementById('selectAllGlobal');

    // If no global select all and no permissions — exit safely
    const allPermissionCheckboxes = document.querySelectorAll('input[name="permission[]"]');
    const moduleSelectAlls = document.querySelectorAll('[id^="selectAllModule"]');

    if (!globalSelectAll && allPermissionCheckboxes.length === 0 && moduleSelectAlls.length === 0) {
        return; // Nothing to initialize
    }

    if (globalSelectAll) {
        globalSelectAll.addEventListener('change', function () {
            const allCheckboxes = document.querySelectorAll('input[name="permission[]"]');
            const allModuleSelects = document.querySelectorAll('[id^="selectAllModule"]');

            allCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });

            allModuleSelects.forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.indeterminate = false;
            });
        });
    }

    // Module Select Alls
    if (moduleSelectAlls.length > 0) {
        moduleSelectAlls.forEach(selectAll => {
            const moduleIndex = selectAll.id.replace('selectAllModule', '');
            const moduleCheckboxes = document.querySelectorAll('.module-' + moduleIndex);

            selectAll.addEventListener('change', function () {
                moduleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                updateGlobalSelectAll();
            });
        });
    }

    // Individual checkbox change
    if (allPermissionCheckboxes.length > 0) {
        allPermissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const classList = Array.from(this.classList);
                const moduleClass = classList.find(cls => cls.startsWith('module-'));
                if (moduleClass) {
                    const moduleIndex = moduleClass.replace('module-', '');
                    updateModuleSelectAll(moduleIndex);
                }

                updateGlobalSelectAll();
            });
        });
    }

    function updateModuleSelectAll(moduleIndex) {
        const moduleCheckboxes = document.querySelectorAll('.module-' + moduleIndex);
        const moduleSelectAll = document.getElementById('selectAllModule' + moduleIndex);

        if (!moduleSelectAll || moduleCheckboxes.length === 0) return;

        const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

        moduleSelectAll.checked = allChecked;
        moduleSelectAll.indeterminate = someChecked && !allChecked;
    }

    function updateGlobalSelectAll() {
        const globalSelectAll = document.getElementById('selectAllGlobal');
        const allCheckboxes = document.querySelectorAll('input[name="permission[]"]');

        if (!globalSelectAll || allCheckboxes.length === 0) return;

        const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);

        globalSelectAll.checked = allChecked;
        globalSelectAll.indeterminate = someChecked && !allChecked;
    }
});

(function () {
    'use strict';

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePermissions);
    } else {
        initializePermissions();
    }

    function initializePermissions() {
        const globalSelectAll = document.getElementById('selectAllGlobal');
        const allPermissionCheckboxes = document.querySelectorAll('input[name="permission[]"]');
        const moduleSelectAlls = document.querySelectorAll('[id^="selectAllModule"]');

        // If there are no permissions or select-alls, safely exit
        if (!globalSelectAll && moduleSelectAlls.length === 0 && allPermissionCheckboxes.length === 0) {
            return;
        }

        // Initialize select all states after short delay
        setTimeout(initializeSelectAllStates, 100);

        // Global Select All logic
        if (globalSelectAll) {
            globalSelectAll.addEventListener('change', function () {
                const allCheckboxes = document.querySelectorAll('input[name="permission[]"]');
                const allModuleSelects = document.querySelectorAll('[id^="selectAllModule"]');

                allCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                allModuleSelects.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    checkbox.indeterminate = false;
                });
            });
        }

        // Module Select All logic
        if (moduleSelectAlls.length > 0) {
            moduleSelectAlls.forEach(selectAll => {
                const moduleIndex = selectAll.id.replace('selectAllModule', '');
                const moduleCheckboxes = document.querySelectorAll('.module-' + moduleIndex);

                selectAll.addEventListener('change', function () {
                    moduleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateGlobalSelectAll();
                });
            });
        }

        // Individual checkbox change logic
        if (allPermissionCheckboxes.length > 0) {
            allPermissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const moduleClass = Array.from(this.classList).find(cls => cls.startsWith('module-'));
                    if (moduleClass) {
                        const moduleIndex = moduleClass.replace('module-', '');
                        updateModuleSelectAll(moduleIndex);
                    }
                    updateGlobalSelectAll();
                });
            });
        }
    }

    function initializeSelectAllStates() {
        const moduleSelectAlls = document.querySelectorAll('[id^="selectAllModule"]');
        if (moduleSelectAlls.length > 0) {
            moduleSelectAlls.forEach(selectAll => {
                const moduleIndex = selectAll.id.replace('selectAllModule', '');
                updateModuleSelectAll(moduleIndex);
            });
        }
        updateGlobalSelectAll();
    }

    function updateModuleSelectAll(moduleIndex) {
        const moduleCheckboxes = document.querySelectorAll('.module-' + moduleIndex);
        const moduleSelectAll = document.getElementById('selectAllModule' + moduleIndex);

        if (!moduleSelectAll || moduleCheckboxes.length === 0) return;

        const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

        moduleSelectAll.checked = allChecked;
        moduleSelectAll.indeterminate = someChecked && !allChecked;
    }

    function updateGlobalSelectAll() {
        const globalSelectAll = document.getElementById('selectAllGlobal');
        const allCheckboxes = document.querySelectorAll('input[name="permission[]"]');

        if (!globalSelectAll || allCheckboxes.length === 0) return;

        const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);

        globalSelectAll.checked = allChecked;
        globalSelectAll.indeterminate = someChecked && !allChecked;
    }
})();

$(document).ready(function () {
    const newsLangSelect = $('#news_language_id');
    let channelSelectWrapper;
    let topicSelectWrapper;

    const topicSelect = $('#select-topic');

    if (topicSelect.length && topicSelect.closest('div.col-sm-6').length) {
        topicSelectWrapper = topicSelect.closest('div.col-sm-6');
    } else {
        topicSelectWrapper = $('div.topic-none');
    }

    const channelSelect = $('#add_channel_id');
    if (channelSelect.length && channelSelect.closest('div.col-sm-6').length) {
        channelSelectWrapper = channelSelect.closest('div.col-sm-6');
    } else {
        channelSelectWrapper = $('div.channel-none');
    }

    if (!newsLangSelect.length) return;

    const channelPlaceholder = channelSelect.length ? channelSelect.find('option:first').clone() : null;
    const topicPlaceholder = topicSelect.length ? topicSelect.find('option:first').clone() : null;

    const selectedLangId = newsLangSelect.val() || null;
    const selectedChannelId = channelSelect.length ? channelSelect.val() : null;
    const selectedTopicId = topicSelect.length ? topicSelect.val() : null;

    function resetSelect(selectEl, placeholder) {
        if (selectEl.length && placeholder) {
            selectEl.empty().append(placeholder.clone());
        }
    }

    function toggleVisibility(wrapper, show) {
        if (show) {
            wrapper.removeClass('d-none');
        } else {
            wrapper.addClass('d-none');
        }
    }

    function loadLanguageData(newsLangId, selectedChannelId = null, selectedTopicId = null, showPopup = false) {
        if (!newsLangId) {
            resetSelect(channelSelect, channelPlaceholder);
            resetSelect(topicSelect, topicPlaceholder);
            toggleVisibility(channelSelectWrapper, false);
            toggleVisibility(topicSelectWrapper, false);
            return;
        }

        let channelPromise = $.Deferred();
        let topicPromise = $.Deferred();

        // Fetch channels
        if (channelSelect.length) {
            $.ajax({
                url: '/admin/get-channels-by-language',
                data: { news_language_id: newsLangId },
                success: function (res) {
                    resetSelect(channelSelect, channelPlaceholder);
                    if (res.channels?.length) {
                        res.channels.forEach(c => {
                            const selected = (selectedChannelId == c.id) ? 'selected' : '';
                            channelSelect.append(`<option value="${c.id}" ${selected}>${c.name}</option>`);
                        });
                        toggleVisibility(channelSelectWrapper, true);
                        channelPromise.resolve(res.channels.length);
                    } else {
                        toggleVisibility(channelSelectWrapper, false);
                        channelPromise.resolve(0);
                    }
                },
                error: () => channelPromise.resolve(-1)
            });
        } else {
            channelPromise.resolve(-1);
        }

        // Fetch topics
        if (topicSelect.length) {
            $.ajax({
                url: '/admin/get-topics-by-language',
                data: { news_language_id: newsLangId },
                success: function (res) {
                    resetSelect(topicSelect, topicPlaceholder);
                    if (res.topics?.length) {
                        res.topics.forEach(t => {
                            const selected = (selectedTopicId == t.id) ? 'selected' : '';
                            topicSelect.append(`<option value="${t.id}" ${selected}>${t.name}</option>`);
                        });
                        toggleVisibility(topicSelectWrapper, true);
                        topicPromise.resolve(res.topics.length);
                    } else {
                        toggleVisibility(topicSelectWrapper, false);
                        topicPromise.resolve(0);
                    }
                },
                error: () => topicPromise.resolve(-1)
            });
        } else {
            topicPromise.resolve(-1);
        }

        if (showPopup) {
            $.when(channelPromise, topicPromise).done(function (channelCount, topicCount) {
                let hasNoChannel = (channelSelect.length > 0 && channelCount === 0);
                let hasNoTopic = (topicSelect.length > 0 && topicCount === 0);

                if (hasNoChannel && hasNoTopic) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: window.trans_channel_topic_not_available,
                        confirmButtonText: 'OK'
                    });
                } else if (hasNoChannel) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: window.trans_channel_not_available,
                        confirmButtonText: 'OK'
                    });
                } else if (hasNoTopic) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: window.trans_topic_not_available,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    }

    // On language change
    newsLangSelect.on('change', function () {
        const newsLangId = $(this).val();
        loadLanguageData(newsLangId, null, null, true);
    });


    // Load existing selections in edit mode
    if (selectedLangId) {
        loadLanguageData(selectedLangId, selectedChannelId, selectedTopicId);
    } else {
        resetSelect(channelSelect, channelPlaceholder);
        resetSelect(topicSelect, topicPlaceholder);
        toggleVisibility(channelSelectWrapper, false);
        toggleVisibility(topicSelectWrapper, false);
    }
});

$(document).ready(function () {
    // Trigger when the modal is opened
    $('#editRssFeedModal').on('shown.bs.modal', function () {
        const newsLangSelect = $('#edit_news_language_id');
        const channelSelect = $('#edit_channel_name');
        const topicSelect = $('#edit_topic_name');

        // Check if news language dropdown exists
        if (!newsLangSelect.length) {
            return;
        }

        // Create placeholders dynamically
        const channelPlaceholder = channelSelect.length ? channelSelect.find('option:first').clone() : null;
        const topicPlaceholder = topicSelect.length ? topicSelect.find('option:first').clone() : null;

        // Detect preselected values (useful for edit modal)
        const selectedLangId = newsLangSelect.val() || null;
        const selectedChannelId = channelSelect.length ? channelSelect.val() : null;
        const selectedTopicId = topicSelect.length ? topicSelect.val() : null;

        function resetSelect(selectEl, placeholder) {
            if (selectEl.length && placeholder) {
                selectEl.empty().append(placeholder.clone());
            }
        }

        function loadLanguageData(newsLangId, selectedChannelId = null, selectedTopicId = null, showPopup = false) {
            if (!newsLangId) {
                resetSelect(channelSelect, channelPlaceholder);
                resetSelect(topicSelect, topicPlaceholder);
                return;
            }

            let channelPromise = $.Deferred();
            let topicPromise = $.Deferred();

            // Fetch channels
            if (channelSelect.length) {
                $.ajax({
                    url: '/admin/get-channels-by-language',
                    data: { news_language_id: newsLangId },
                    success: function (res) {
                        resetSelect(channelSelect, channelPlaceholder);
                        if (res.channels?.length) {
                            res.channels.forEach(c => {
                                const selected = (selectedChannelId == c.id) ? 'selected' : '';
                                channelSelect.append(`<option value="${c.id}" ${selected}>${c.name}</option>`);
                            });
                            channelPromise.resolve(res.channels.length);
                        } else {
                            channelPromise.resolve(0);
                        }
                    },
                    error: () => channelPromise.resolve(-1)
                });
            } else {
                channelPromise.resolve(-1);
            }

            // Fetch topics
            if (topicSelect.length) {
                $.ajax({
                    url: '/admin/get-topics-by-language',
                    data: { news_language_id: newsLangId },
                    success: function (res) {
                        resetSelect(topicSelect, topicPlaceholder);
                        if (res.topics?.length) {
                            res.topics.forEach(t => {
                                const selected = (selectedTopicId == t.id) ? 'selected' : '';
                                topicSelect.append(`<option value="${t.id}" ${selected}>${t.name}</option>`);
                            });
                            topicPromise.resolve(res.topics.length);
                        } else {
                            topicPromise.resolve(0);
                        }
                    },
                    error: () => topicPromise.resolve(-1)
                });
            } else {
                topicPromise.resolve(-1);
            }

            if (showPopup) {
                $.when(channelPromise, topicPromise).done(function (channelCount, topicCount) {
                    let hasNoChannel = (channelSelect.length > 0 && channelCount === 0);
                    let hasNoTopic = (topicSelect.length > 0 && topicCount === 0);

                    if (hasNoChannel && hasNoTopic) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: window.trans_channel_topic_not_available,
                            confirmButtonText: 'OK'
                        });
                    } else if (hasNoChannel) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: window.trans_channel_not_available,
                            confirmButtonText: 'OK'
                        });
                    } else if (hasNoTopic) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: window.trans_topic_not_available,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        }

        // On language change
        newsLangSelect.on('change', function () {
            const newsLangId = $(this).val();
            loadLanguageData(newsLangId, null, null, true);
        });


        // Load existing selections when modal opens
        if (selectedLangId) {
            loadLanguageData(selectedLangId, selectedChannelId, selectedTopicId);
        } else {
            resetSelect(channelSelect, channelPlaceholder);
            resetSelect(topicSelect, topicPlaceholder);
        }
    });
});


tinymce.init({
    selector: '#tinymce_editor',
    height: 400,
    plugins: 'link image code lists',
    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image code',
});