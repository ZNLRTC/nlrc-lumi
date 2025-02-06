<?php

namespace Database\Seeders;

use App\Models\Courses\Unit;
use App\Models\Courses\Topic;
use App\Models\Courses\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Masterminds\HTML5;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kmh = Course::create([
            'name' => 'Kyl mä hoidan', 
            'internal_name' => 'KMH beginners course', 
            'slug' => 'kmh',
            'description' => 'Beginners\' Finnish course for independent study prior to actual language training.',
        ]);
    
        $main = Course::create([
            'name' => 'Mä hoidan', 
            'internal_name' => 'main', 
            'slug' => 'main',
            'description' => 'Main language course at NLRC.',
        ]);

        $nmh = Course::create([
            'name' => 'No mä hoidan', 
            'internal_name' => 'No mä hoidan (advanced)', 
            'slug' => 'nmh',
            'description' => 'Advanced language course at NLRC.',
        ]);
  
        // Unit 1 content manually so instructors testing the site can see at least something real
        $unit1 = Unit::create([
            'course_id' => $main->id,
            'sort' => 1,
            'name' => 'Unit 1',
            'internal_name' => 'Unit 1 (main)',
            'slug' => 'unit-1',
            'description' => 'In this unit, you review topics from the beginners\' course, such as numbers and pronunciation. You will also learn vocabulary for healthcare professions and learn how to use negative possessive clauses.',
        ]);

        $topicsForUnit1 = [
            ['title' => 'Healthcare professions', 'sort' => 1, 'slug' => 'u1-healthcare-professionals', 'description' => 'Review these from the beginners\' course.', 
            'content' => <<<'HTML'
            <section>
            <audio controls preload="none" src="/audio/u1_professions.mp3"></audio>
    
            <table class="sanasto">
                <caption>doctors</caption>
    
                <thead>
                    <tr>
                        <th>
                            Finnish
                        </th>
    
                        <th>
                            English
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <td>
                            lääkäri
                        </td>
    
                        <td>
                            a doctor
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            yleis.lääkäri
                        </td>
    
                        <td>
                            a general practitioner
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            kirurgi
                        </td>
    
                        <td>
                            a surgeon
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            hammas.lääkäri
                        </td>
    
                        <td>
                            a dentist <span class="norm">(literally,</span> a tooth doctor<span class="norm">)</span>
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <p>Terms for nurses vary depending on what kind of education the person in question has. Their titles end in the word <i>hoitaja</i>, literally <i>a caretaker</i> or <i>a person who treats</i>, derived from the verb <i>hoitaa</i> (<i>to take care, to nurse, to give care</i>). The Finnish title that roughly corresponds to a college degree in nursing in many other countries is <i>sairaan.hoitaja</i>, literally <i>a caretaker of the sick</i> (from the noun <i>sairas</i> : <i>sairaa</i>-, <i>sick</i>). </p>
    
            <audio controls preload="none" src="/audio/u1_professions2.mp3"></audio>
    
            <table class="sanasto">
                <caption>nurses</caption>
    
                <thead>
                    <tr>
                        <th>
                            Finnish
                        </th>
    
                        <th>
                            English
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <td>
                            sairaan.hoitaja
                        </td>
    
                        <td>
                            a nurse <span class="norm">(in Finland, has a 4-year degree or higher from a vocational university)</span>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            ensi.hoitaja
                        </td>
    
                        <td>
                            a paramedic
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            terveyden.hoitaja
                        </td>
    
                        <td>
                            a public health nurse
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            kätilö
                        </td>
    
                        <td>
                            a midwife
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            lähi.hoitaja
                        </td>
    
                        <td>
                            a licensed practical nurse <span class="norm">(in Finland, has from 1,5 to 3,5 years degree or higher from a vocational school)</span>
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <hr class='pien' />
    
            <p>The following are not healthcare professionals but support workers or just general work-related terms. Study them as well.</p>
    
            <audio controls preload="none" src="/audio/u1_professions3.mp3"></audio>
    
            <table class="sanasto">
                <caption>other work-related terms</caption>
    
                <thead>
                    <tr>
                        <th>
                            Finnish
                        </th>
    
                        <th>
                            English
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <td>
                            hoiva-avustaja
                        </td>
    
                        <td>
                            nursing assistant
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            siivooja
                        </td>
    
                        <td>
                            a cleaner
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            työ : töissä
                        </td>
    
                        <td>
                            work : at work
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Kuuntele ammatit.</p>
    
            <p>Listen and figure out the profession of each person. The list below has Finnish first names. You are not expected to understand the entire clip. Just try to catch a profession in it.</p>
    
            <ol class='printtiviivat'>
                <li>Laura<br />
    
                    <audio controls preload="none" src="/audio/u1e1i1.mp3">u1e1i1</audio>
                </li>
    
                <li>Taavi<br />
    
                    <audio controls preload="none" src="/audio/u1e1i2.mp3">u1e1i2</audio>
                </li>
    
                <li>Alina<br />
    
                    <audio controls preload="none" src="/audio/u1e1i3.mp3">u1e1i3</audio>
                </li>
    
                <li>Oskari<br />
    
                    <audio controls preload="none" src="/audio/u1e1i4.mp3">u1e1i4</audio>
                </li>
            </ol>
    
            <details>
                <summary>Check answers</summary>

                <ol>
                    <li>Moi! Mä oon Laura. Mä oon lääkäri ja mä oon töissä isossa sairaalassa.<br /><i>Hello! I'm Laura. I'm a doctor and I work in a big hospital.</i>
                    </li>
            
                    <li>Hei! Mun nimi on Taavi. Mä pidän työpaikan siistinä eli mä oon siivooja.<br /><i>Hello! My name is Taavi. I keep the working place clean, that is, I'm a cleaner.</i>
                    </li>
            
                    <li>Moikka! Mä oon Alina. Mä huolehdin ihmisten hampaista. Mä oon hammaslääkäri.<br /><i>Hi there! I'm Alina. I take care of people's teeth. I'm a dentist.</i>
                    </li>
            
                    <li>Moi! Mä oon Oskari. Ammatti on ensihoitaja. Mä liikun töissä ambulanssilla.<br /><i>Hi! I am Oskari. <span class="norm">[</span>My<span class="norm">]</span> profession is a paramedic. At work, I move around with an ambulance.</i>
                    </li>
                </ol>

            </details>
        </section>
    
        <section class="exercise" id='u1-lue1'>
            <h2>Exercise</h2>
    
            <p>Kuuntele ja lue perässä.</p>
    
            <p>Listen to the clip and then say out loud. You do not have understand the sentences at this point. This is a pronunciation exercise.</p>
    
            <ol>
                <li>Kätilö auttaa äitiä synnytyksessä.
    
                    <audio controls preload="none" src="/audio/u1e2i1.mp3">u1e2i1</audio>
                </li>
    
                <li>Terveydenhoitajan työpaikka voi olla koulussa.
    
                    <audio controls preload="none" src="/audio/u1e2i2.mp3">u1e2i2</audio>
                </li>
    
                <li>Suomessa on hoitaja.pula.
    
                    <audio controls preload="none" src="/audio/u1e2i3.mp3">u1e2i3</audio>
                </li>
    
                <li>Ensihoitaja hoitaa potilaita, jotka tarvitsevat kiireellistä hoitoa.
    
                    <audio controls preload="none" src="/audio/u1e2i4.mp3">u1e2i4</audio>
                </li>
    
                <li>Lähihoitaja voi olla töissä esimerkiksi hoivakodissa, päiväkodissa tai sairaalassa.
    
                    <audio controls preload="none" src="/audio/u1e2i5.mp3">u1e2i5</audio>
                </li>
    
                <li>Kirurgi tekee leikkauksia.
    
                    <audio controls preload="none" src="/audio/u1e2i6.mp3">u1e2i6</audio>
                </li>
    
                <li>Hoiva-avustaja on yleensä töissä vanhus- tai vammaispalveluissa.
    
                    <audio controls preload="none" src="/audio/u1e2i7.mp3">u1e2i7</audio>
                </li>
    
                <li>Hammaslääkäri diagnosoi ja hoitaa suun sairauksia.
    
                    <audio controls preload="none" src="/audio/u1e2i8.mp3">u1e2i8</audio>
                </li>
            </ol>
    
            <details>
                <summary>Check answers</summary>

                <ol>
                    <li>A midwife helps with giving birth.</li>
            
                    <li>A public health nurse workplace might be a school.</li>
            
                    <li>There is a shortage of nurses in Finland.</li>
            
                    <li>A paramedic treats patients that need urgent care.</li>
            
                    <li>A practical nurse can work at a nursing home, a kindergarten, or a hospital, for example.</li>
            
                    <li>A surgeon performs surgeries.</li>
            
                    <li>A nursing assistant usually work at elderly or disabled care.</li>
            
                    <li>A dentist diagnoses and treats oral diseases.</li>
                </ol>

            </details>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Sano suomeksi.</p>
    
            <p>Express a similar idea in Finnish. If this feels difficult, go back to the <i>Kyl mä hoidan</i> course and review the verb <i>to be</i> and yes–no questions.</p>
    
            <ol class='printtiviivat'>
                <li>I'm a nurse.</li>
    
                <li>She's a doctor.</li>
    
                <li>Emma<sup>name</sup> is a practical nurse.</li>
    
                <li>I'm not a surgeon.</li>
    
                <li>Are you a nurse?</li>
    
                <li>Is Lauri<sup>name</sup> a paramedic?</li>
    
                <li>The doctor is not here. (<i>here</i> = <i>täällä</i>)</li>
    
                <li>Where's the cleaner?</li>
            </ol>

            <details>
                <summary>Check answers</summary>

                <audio controls preload="none" src="/audio/u1e3.mp3">u1e3</audio>

                <ol>
                    <li>Mä oon sairaan.hoitaja.</li>

                    <li>Se on lääkäri.</li>

                    <li>Emma on lähi.hoitaja.</li>

                    <li>Mä en oo kirurgi.</li>

                    <li>Oot(ko) sä sairaan.hoitaja?</li>

                    <li>Onko ~ Onks Lauri ensi.hoitaja?</li>

                    <li>Lääkäri ei oo täällä.</li>

                    <li>Missä siivooja on?</li>
                </ol>
            </details>
        </section>
HTML
            ],
            ['title' => 'Job-related adjectives', 'sort' => 2, 'description' => 'Study these.', 'slug' => 'u1-job-adjectives', 
            'content' => <<<'HTML'
<section>
    
    <p>If you compare words with adjectives in Finnish, like <i>I'm younger than you</i>, there is no word for <i>more</i>. Instead, Finnish adds <i>‑mpi</i> to the adjective, as shown in the middle column. The right column is the superlative form, the highest degree. It usually has <i>‑in</i> at the end. </p>

    <audio controls preload="none" src="/audio/u1-adj.mp3">u1-adj</audio>

    <table class="sanasto nelja">
        <caption>adjectives</caption>

        <tbody>
            <tr>
                <td>
                    kiva
                </td>

                <td>
                    kivempi
                </td>

                <td>
                    kivoin
                </td>

                <td>
                    nice, nicer, the nicest
                </td>
            </tr>

            <tr>
                <td>
                    helppo
                </td>

                <td>
                    helpompi
                </td>

                <td>
                    helpoin
                </td>

                <td>
                    easy, easier, the easiest
                </td>
            </tr>

            <tr>
                <td>
                    vaikee
                </td>

                <td>
                    vaikeempi
                </td>

                <td>
                    vaikein
                </td>

                <td>
                    difficult, hard; more difficult, harder; the most difficult, the hardest
                </td>
            </tr>

            <tr>
                <td>
                    hyvä
                </td>

                <td>
                    parempi
                </td>

                <td>
                    paras
                </td>

                <td>
                    good, better, the best
                </td>
            </tr>

            <tr>
                <td>
                    haastava
                </td>

                <td>
                    haastavampi
                </td>

                <td>
                    haastavin
                </td>

                <td>
                    challenging, more challening, the most challenging
                </td>
            </tr>
        </tbody>
    </table>

    <table class="esimerkki">
        <caption>in context</caption>

        <tbody>
            <tr>
                <td>
                    Mun työ on rankka.
                </td>

                <td>
                    My job is tough.
                </td>
            </tr>

            <tr>
                <td>
                    Maanantai oli helppo.
                </td>

                <td>
                    Monday was easy.
                </td>
            </tr>
        </tbody>
    </table>

    <p>If you want to compare things, the word for <i>than</i> in the context of comparisons is <i>kuin</i>. In colloquial Finnish, people just shorten it to <i>ku</i>.</p>

    <table class="esimerkki">
        <caption>sample comparisons</caption>

        <tbody>
            <tr>
                <td>
                    Tää on parempi <span class="ala">ku</span> toi.
                </td>

                <td>
                    This one is better <span class="ala">than</span> that.
                </td>
            </tr>

            <tr>
                <td>
                    Iltavuoro oli rankempi <span class="ala">ku</span> eilen.
                </td>

                <td>
                    The afternoon shift was tougher <span class="ala">than</span> yesterday.
                </td>
            </tr>
        </tbody>
    </table>

    <p>In later listings in this book, the translations only give you the basic form.</p>
</section>

<section class="exercise">
    <h2 >Exercise</h2>

    <p>Sano suomeksi.</p>

    <p>Express a similar idea in Finnish.</p>

    <ol class='printtiviivat'>
        <li>A night shift is easier than a morning shift.</li>

        <li>That one is the best.</li>

        <li>The surgery (<i>leikkaus</i>) is challenging.</li>

        <li>The new (<i>uusi</i>) doctor is better than the old (<i>vanha</i>) doctor.</li>
    </ol>

    <details>
        <summary>Check answers</summary>
        <audio controls preload="none" src="/audio/u1e4.mp3">u1e4</audio>

        <ol>
            <li>Yö.vuoro on helpompi kuin aamu.vuoro.</li>
    
            <li>Toi on paras.</li>
    
            <li>Leikkaus on haastava.</li>
    
            <li>Uusi lääkäri on parempi kuin vanha [lääkäri].</li>
        </ol>
    </details>
</section>

<section class="exercise" id="u1-adj-kuuntelu">
    <h2 >Exercise</h2>

    <p>Kuuntele adjektiivi.</p>

    <p>Listen to the clips and try to catch the adjective in each. You are not expected to understand the entire clip. </p>

    <ol>
        <li><audio controls preload="none" src="/audio/u1e5i1.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e5i2.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e5i3.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e5i4.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e5i5.mp3"></audio></li>
    </ol>

    <details>
        <summary>Check answers</summary>

        <ol>
            <li>Antti on tosi kiva.<br /><i>Antti is a really nice person.</i>                    </li>
    
            <li>Tää kirja on niin vaikee!<br /><i>This book is so difficult!</i></li>
    
            <li>Saatto.hoito on haastava työ.<br /><i>Terminal care is a challenging task ~ work.</i></li>
    
            <li>Perjantai on viikon paras päivä.<br /><i>Friday is the best day of the week.</i></li>
    
            <li>Töihin on helpoin tulla bussilla.<br /><i>Arriving ~ Getting to work is easiest by bus.</i></li>
        </ol>

    </details>
</section>
HTML
            ],
            ['title' => 'Names in Finland', 'sort' => 3, 'description' => 'Most people have at least one.', 'slug' => 'u1-names', 
            'content' => <<<'HTML'
<section>  
            <p>In Finland, people are legally required to have a last name (~ a family name, <i>suku.nimi</i>) and a first name (<i>etu.nimi</i>). Many people have more than one first name, but only one is usually used to address the person while the other first names might only appear in official documents, like a passport. Legally, a person can have four first names.</p>
    
            <p>Middle names (<i>väli.nimi</i>) are not a common cultural concept in Finland, and many people in Finland might give you their second first name if you asked for their middle name.</p>
    
            <p>The most common last names in Finland are <i>Korhonen</i>, <i>Virtanen</i>, <i>Mäkinen</i>, <i>Nieminen</i>, <i>Mäkelä</i>, and <i>Hämäläinen</i>.
            </p>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Lue nimet ääneen.</p>
    
            <p>For the sake of practicing pronunciation, read these names out loud. Hyphens in names are not pronounced.</p>
    
            <ol>
                <li>Pekka Makkonen</li>
    
                <li>Taru Haavisto</li>
    
                <li>Veeti Aaltonen</li>
    
                <li>Miina Kivi.mäki</li>
    
                <li>Otso Nurminen-Rekola</li>
    
                <li>Maiju Lappi</li>
    
                <li>Rauli Toivonen</li>
    
                <li>Jenni Leskinen</li>
    
                <li>Juha-Matti Meri</li>
    
                <li>Janna Karhu-Rantala</li>
    
                <li>Ahti Räisänen</li>
    
                <li>Sofia Viinanen</li>
    
                <li>Sakari Jaakkola</li>
    
                <li>Riitta Kivelä</li>
            </ol>
    
            <audio controls preload="none" src="/audio/u1e6.mp3"></audio>
        </section>
HTML
            ],
            ['title' => 'Spelling letter by letter', 'sort' => 4, 'description' => 'How to talk about individual letters.', 'slug' => 'u1-spelling', 
            'content' => <<<'HTML'
<section>    
            <p>Sometimes, you might need to spell out your name for a waiter or a clerk in Finland. You might also have to solve some misunderstandings by saying what you actually meant, letter by letter.</p>
    
            <p>The list below shows how to pronounce the name of an individual letter in Finnish, using Finnish sounds. You need to pronounce a long vowel even if you are talking about a single letter for a vowel.</p>
    
            <audio controls preload="none" src="/audio/u1-spelling-letter-by-letter.mp3"></audio>
    
            <table class="sanasto">
                <caption>pronunciation of the characters in the finnish alphabet</caption>
    
                <thead>
                    <tr>
                        <th>
                            spelling
                        </th>
    
                        <th>
                            Finnish pronunciation
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <td>
                            a
                        </td>
    
                        <td>
                            aa
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            b
                        </td>
    
                        <td>
                            bee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            c
                        </td>
    
                        <td>
                            see
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            d
                        </td>
    
                        <td>
                            dee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            e
                        </td>
    
                        <td>
                            ee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            f
                        </td>
    
                        <td>
                            äf
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            g
                        </td>
    
                        <td>
                            gee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            h
                        </td>
    
                        <td>
                            hoo
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            i
                        </td>
    
                        <td>
                            ii
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            j
                        </td>
    
                        <td>
                            jii
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            k
                        </td>
    
                        <td>
                            koo
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            l
                        </td>
    
                        <td>
                            äl
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            m
                        </td>
    
                        <td>
                            äm
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            n
                        </td>
    
                        <td>
                            än
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            o
                        </td>
    
                        <td>
                            oo
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            p
                        </td>
    
                        <td>
                            pee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            q
                        </td>
    
                        <td>
                            kuu
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            r
                        </td>
    
                        <td>
                            är
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            s
                        </td>
    
                        <td>
                            äs
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            t
                        </td>
    
                        <td>
                            tee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            u
                        </td>
    
                        <td>
                            uu
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            v
                        </td>
    
                        <td>
                            vee
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            w
                        </td>
    
                        <td>
                            tupla.vee ~ kaksois.vee <span class="norm">(literally</span> a double v<span class="norm">)</span>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            x
                        </td>
    
                        <td>
                            äks
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            y
                        </td>
    
                        <td>
                            yy
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            z
                        </td>
    
                        <td>
                            tset ~ tseta
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            å
                        </td>
    
                        <td>
                            ruotsalainen oo <span class="norm">(lit. </span>a Swedish O<span class="norm">)</span>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            ä
                        </td>
    
                        <td>
                            ää
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            ö
                        </td>
    
                        <td>
                            öö
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <p>Characters <i>å</i>, <i>ä</i>, and <i>ö</i> are the last letters of the Finnish alphabet if you ever need to sort things in an alphabetical order.</p>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Lue dialogit ääneen.</p>
    
            <p>Read the dialogues out loud.</p>
    
            <ol class="dialogi">
                <li>
                    <ul>
                        <li>Sanotko vielä sun etunimen?</li>
    
                        <li>Santiago.</li>
    
                        <li>Miten se kirjotetaa?</li>
    
                        <li>S - A - N - T - I - A - G - O.</li>
    
                        <li>Kiitti.</li>
                    </ul>
    
                    <ul>
                        <li>Can you say your first name?</li>
    
                        <li>Santiago.</li>
    
                        <li>How do you spell it?</li>
    
                        <li>S - A - N - T - I - A - G - O.</li>
    
                        <li>Thanks</li>
                    </ul>
                </li>
    
                <li>
                    <ul>
                        <li>Mä tarvin vielä sun sukunimen.</li>
    
                        <li>Mahgoup.</li>
    
                        <li>Sori, sanotko uudestaan?</li>
    
                        <li>Mahgoup. M - A - H - G - O - U - P.</li>
    
                        <li>Okei.</li>
                    </ul>
    
                    <ul>
                        <li>And then I need your last name.</li>
    
                        <li>Mahgoup.</li>
    
                        <li>Sorry, can you say that again?</li>
    
                        <li>Mahgoup. M - A - H - G - O - U - P.</li>
    
                        <li>Got it.</li>
                    </ul>
                </li>
    
                <li>
                    <ul>
                        <li>Onks Chi-Cheng sun veli?</li>
    
                        <li>Kuka?</li>
    
                        <li>C - H - I - C - H - E - N - G.</li>
    
                        <li>Aaa, joo.</li>
                    </ul>
    
                    <ul>
                        <li>Is Chi-Cheng your brother?</li>
    
                        <li>Who?</li>
    
                        <li>C - H - I - C - H - E - N - G.</li>
    
                        <li>Oh, yeah.</li>
                    </ul>
                </li>
    
                <li>
                    <ul>
                        <li>Mikä teidän sukunimi on?</li>
    
                        <li>Kankaan.pää.</li>
    
                        <li>Miten se kirjotetaan?</li>
    
                        <li>K - A - N - K - A - A - N - P - Ä - Ä.</li>
    
                        <li>Kiitos.</li>
                    </ul>
    
                    <ul>
                        <li>What is your last name?</li>
    
                        <li>Kankaanpää.</li>
    
                        <li>How do you spell it?</li>
    
                        <li>K - A - N - K - A - A - N - P - Ä - Ä.</li>
    
                        <li>Thank you.</li>
                    </ul>
                </li>
            </ol>
        </section>
    
        <section class="exercise" id="u1-nimikuuntelu1">
            <h2>Exercise</h2>
    
            <p>Kuuntele ja kirjoita nimet ylös.</p>
    
            <p>Listen to clips and write down the first names you hear spelled out.</p>
    
            <ol class="dialogi">
                <li><audio controls preload="none" src="/audio/u1e8i1.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e8i2.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e8i3.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e8i4.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e8i5.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e8i6.mp3"></audio></li>
            </ol>
    
            <details>
                <summary>Check answers</summary>
                <ol>
                    <li>Leo Toivonen</li>
            
                    <li>Kerttu Junnila</li>
            
                    <li>Pinja Mäkinen</li>
            
                    <li>Julius Grön</li>
            
                    <li>Siiri Hyyryläinen</li>
            
                    <li>Helge Ahola</li>
                </ol>
            </details>
        </section>
HTML
            ],
            ['title' => 'Negative possessive clause', 'sort' => 5, 'description' => 'How to say that you do not have or do not possess something.', 'slug' => 'u1-negative-possessive-clause', 
            'content' => <<<'HTML'
<section id='negative-poss-clause'>
    
    <p>This section requires prior knowledge about possessive clauses (the <i>mulla on...</i> clauses). This was covered in the <i>Kyl mä hoidan</i> beginners' course. Make sure you know how to make positive possessive clauses before reading on.</p>

    <table class="esimerkki">
        <caption>negative possessive clauses</caption>

        <tbody>
            <tr>
                <td>
                    Mulla ei oo rahaa.
                </td>

                <td>
                    I don't have money.
                </td>
            </tr>

            <tr>
                <td>
                    Lääkärillä ei oo aikaa.
                </td>

                <td>
                    The doctor doesn't have time.
                </td>
            </tr>

            <tr>
                <td>
                    Annilla ei oo siskoo.
                </td>

                <td>
                    Anni<sup>name</sup> doesn't have a sister.
                </td>
            </tr>
        </tbody>
    </table>

    <audio controls preload="none" src="/audio/u1-neg-poss.mp3"></audio>

    <p>In a positive possessive clause, the verb always remains in third person, and negative possessive clauses are no different. You would start with the owner + <i>‑llA</i> as usual, and then add <i>ei oo</i>, making the structure look like this:</p>

    <p class="malli"><span>X<sup>owner</sup> + <i>‑llA</i></span> <span><i>ei oo</i></span> <span>Y<sup>owned</sup> + -V</span></p>

    <p>In standard written Finnish, people would write <i>ei ole</i> rather than <i>ei oo</i>, but you should use the colloquial <i>ei oo</i> in all your everyday interactions.</p>

    <p>The <i>‑V</i> at the end of the owned thing is the <a href="unit5.xhtml#u5-partitive">partitive ending</a>, usually a long vowel if the word ends in a vowel. It will be discussed in unit 5 in further detail, but you already saw the same thing in <i>Kyl mä hoidan</i> with nouns that follow a number. At this point, either lengthen the vowel or add <i>a</i> or <i>ä</i> to get this ending if the word ends in a vowel other than <i>e</i>. If the word ends in an <i>e</i>, add <i>‑ttA</i>. If word ends in a consonant, you can just add <i>‑tA</i> in most cases. If you are writing and have time to double check, look up the partitive form on Wiktionary or other dictionary that shows the word with endings.</p>

    <table class="esimerkki">
        <caption>more negative possessive clauses, partitive endings underlined</caption>

        <tbody>
            <tr>
                <td>
                    Mulla ei oo paita<span class="ala">a</span>.
                </td>

                <td>
                    I don't have a shirt.
                </td>
            </tr>

            <tr>
                <td>
                    Harrilla ei oo kuume<span class="ala">tta</span>.
                </td>

                <td>
                    Harri<sup>name</sup> has no fever.
                </td>
            </tr>

            <tr>
                <td>
                    Meillä ei oo vakuutus<span class="ala">ta</span>.
                </td>

                <td>
                    We don't have an insurance.
                </td>
            </tr>
        </tbody>
    </table>
</section>

<section class="exercise">
    <h2>Exercise</h2>

    <p>Muuta negatiiviseksi.</p>

    <p>Change into negative. Add the partitive ending if the word does not have it already. Partitive endings are underlined in this task for clarity.</p>

    <ol start="0" class='printtiviivat'>
        <li>Mulla on sisko.<br />→ Mulla ei oo sisko<span class="ala">o</span> ~ sisko<span class="ala">a</span>.
        </li>

        <li>Jonnalla on iltavuoro.</li>

        <li>Niillä on ongelma (<i>a problem</i>).</li>

        <li>Mulla on ruoka<span class="ala">a</span> (<i>food</i>).</li>

        <li>Sillä on avain (<i>a key</i>).</li>

        <li>Hoitajalla on aika<span class="ala">a</span> (<i>time</i>).</li>

        <li>Meillä on kurssi (<i>a class</i>) klo 10.</li>
    </ol>

    <details>
        <summary>Check answers</summary>

        <audio controls preload="none" src="/audio/u1e9.mp3"></audio>

        <ol>
            <li>Jonnalla ei oo iltavuoroa.</li>

            <li>Niillä ei oo ongelmaa.</li>

            <li>Mulla ei oo ruokaa.</li>

            <li>Sillä ei oo avainta.</li>

            <li>Hoitajalla ei oo aikaa.</li>

            <li>Meillä ei oo kurssia klo 10.</li>
        </ol>
    </details>
</section>

<section class="exercise">
    <h2>Exercise</h2>

    <p>Sano suomeksi.</p>

    <p>Express a similar idea in Finnish. The translations of the prompts have partitive endings underlined, so you do not have to add partitive separately.</p>

    <ol class='printtiviivat'>
        <li>I don't have money (<i>raha<span class="ala">a</span></i>).</li>

        <li>I don't have work (<i>töi<span class="ala">tä</span></i>) tomorrow.</li>

        <li>This is not difficult (<i>vaikee<span class="ala">ta</span></i>).</li>

        <li>The doctor doesn't have a solution (<i>ratkaisu<span class="ala">u</span></i>).</li>

        <li>We don't have time.</li>

        <li>Anja<sup>name</sup> doesn't have a vacation (<i>loma<span class="ala">a</span></i>)</li>
    </ol>

    <details>
        <summary>Check answers</summary>

        <audio controls preload="none" src="/audio/u1e10.mp3">u1e10</audio>

        <ol>
            <li>Mulla ei oo rahaa.</li>

            <li>Mulla ei oo töitä huomenna.</li>

            <li>Tää ei oo vaikeeta.</li>

            <li>Lääkärillä ei oo ratkaisuu.</li>

            <li>Meillä ei oo aikaa.</li>

            <li>Anjalla ei oo lomaa.</li>
        </ol>
    </details>

</section>

<section class="exercise">
    <h2>Exercise</h2>

    <p>Kuuntele ja kirjoita subjekti.</p>

    <p>Listen to the clips and match the owner or the actor is with each clip. The list of possible answers is given
        in
        English. You are not expected to understand the entire prompt.</p>

    <ol type="a" class='printtiviivat'>
        <li>me ~ the speaker</li>

        <li>you ~ the listener</li>

        <li>us</li>

        <li>Anna<sup>name</sup></li>

        <li>Antti<sup>name</sup></li>

        <li>Saara<sup>name</sup></li>
    </ol>

    <ol>
        <li><audio controls preload="none" src="/audio/u1e11i1.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e11i2.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e11i3.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e11i4.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e11i5.mp3"></audio></li>

        <li><audio controls preload="none" src="/audio/u1e11i6.mp3"></audio></li>
    </ol>

    <details>
        <summary>Check answers</summary>
        <ol class="dialogi">
            <li>b
    
                <ul>
                    <li>Sulla on aamu.vuoro.</li>
    
                    <li>Eikä oo. Huomenna on.</li>
                </ul>
    
                <ul>
                    <li>You have a morning shift.</li>
    
                    <li>No I don't. Tomorrow I have.</li>
                </ul>
            </li>
    
            <li>a

                <ul>
                    <li>Hei.</li>
    
                    <li>Oota. Mulla on kiire!</li>
                </ul>
    
                <ul>
                    <li>Hey.</li>
    
                    <li>Wait. I'm busy.</li>
                </ul>
            </li>
    
            <li>e

                <ul>
                    <li>Missä Antti on?</li>
    
                    <li>Antilla on aika hammas.lääkärille.</li>
    
                    <li>Aha.</li>
                </ul>
    
                <ul>
                    <li>Where's Antti?</li>
    
                    <li>Antti has a dentist appointment.</li>
    
                    <li>Oh.</li>
                </ul>
            </li>
    
            <li>f

                <ul>
                    <li>Saaralla on söpö koira.</li>
    
                    <li>Joo.</li>
                </ul>
    
                <ul>
                    <li>Saara has a cute dog.</li>
    
                    <li>Right?</li>
                </ul>
            </li>
    
            <li>c

                <ul>
                    <li>Aika iso talo.</li>
    
                    <li>Meillä on iso perhe.</li>
                </ul>
    
                <ul>
                    <li>That's a pretty big house.</li>
    
                    <li>We have a big family.</li>
                </ul>
            </li>
    
            <li>d

                <ul>
                    <li>Tuleeks Anna metrolla?</li>
    
                    <li>Joo. Ja Annalla ei oo autoo.</li>
                </ul>
    
                <ul>
                    <li>Is Anna coming by subway?</li>
    
                    <li>Yeah. And she doesn't have a car.</li>
                </ul>
            </li>
        </ol>
    </details>
</section>

<section class="exercise" id="u1-ammatit-kaannos">
    <h2>Exercise</h2>

    <p>Sano suomeksi.</p>

    <p>Express a similar idea in Finnish. These are positive clauses. You do not have to change them into negative.
    </p>

    <ol class='printtiviivat'>
        <li>I have a night shift.</li>

        <li>I have four aunts.</li>

        <li>The doctor has a break (<i>tauko</i>).</li>

        <li>The nurse has a night shift.</li>

        <li>She has two sisters.</li>

        <li>The midwife has a question (<i>kysymys</i>).</li>

        <li>We have a break.</li>

        <li>They have a question.</li>

        <li>The practical nurse has a problem (<i>ongelma</i>).</li>

        <li>The paramedic has a morning shift.</li>
    </ol>

    <details>
        <summary>Check answers</summary>
        
        <audio controls preload="none" src="/audio/u1e12.mp3"></audio>

        <ol>
            <li>Mulla on yö.vuoro.</li>
    
            <li>Mulla on neljä tätiä.</li>
    
            <li>Lääkärillä on tauko.</li>
    
            <li>Sairaan.hoitajalla on yö.vuoro.</li>
    
            <li>Sillä on kaksi siskoo.</li>
    
            <li>Kätilöllä on kysymys.</li>
    
            <li>Meillä on tauko.</li>
    
            <li>Niillä on kysymys.</li>
    
            <li>Lähi.hoitajalla on ongelma.</li>
    
            <li>Ensi.hoitajalla on aamu.vuoro.</li>
        </ol>
    </details>
</section>
HTML
            ],
            ['title' => 'Review numbers', 'sort' => 6, 'description' => 'You will need them for amounts, phone numbers, postal codes, and dates.', 'slug' => 'u1-numbers', 
            'content' => <<<'HTML'
<section>    
            <p>You should know Finnish numbers at this point from the <i>Kyl mä hoidan</i> beginners' course. If you feel like they are a bit shaky, go back and review them now.</p>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Sano puhelinnumerot ääneen.</p>
    
            <p>Read the phone numbers out loud. Read them digit by digit, not as hundreds or thousands.</p>
    
            <ol>
                <li>040 038 2613</li>
    
                <li>040 934 8258</li>
    
                <li>045 571 8835</li>
    
                <li>044 216 8496</li>
    
                <li>043 617 4426</li>
    
                <li>040 363 4843</li>
    
                <li>045 798 0400</li>
    
                <li>+358 42 308 7628 (the + sign in pronounced <span class='ipa'>ˈplus</span>)</li>
            </ol>
    
            <details>
                <summary>Check answers</summary>
                <audio controls preload="none" src="/audio/u1e13.mp3"></audio>

            </details>
        </section>
    
        <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Kuuntele puhelinnumerot.</p>
    
            <p>Listen to the clips and write down the phone numbers you hear.</p>
    
            <ol>
                <li><audio controls preload="none" src="/audio/u1e14i1.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e14i2.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e14i3.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e14i4.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e14i5.mp3"></audio></li>
    
                <li><audio controls preload="none" src="/audio/u1e14i6.mp3"></audio></li>
            </ol>
    
            <details>
                <summary>Check answers</summary>

                <ol>
                    <li>045 980 7721</li>

                    <li>050 662 1374</li>

                    <li>040 554 9431</li>

                    <li>046 301 8745</li>

                    <li>050 873 1950</li>

                    <li>040 321 6549</li>
                </ol>
            </details>
        </section>
HTML
            ],
            ['title' => 'Street addresses', 'sort' => 7, 'description' => 'How addresses are formatted in Finland.', 'slug' => 'u1-addresses', 
            'content' => <<<'HTML'
<section>
   
   <p>This will be discussed in detail later in unit 9, but here is an outline of how Finnish street addresses
       work.
   </p>

   <p>In Finland, a street address has the following:</p>

   <ul>
       <li>street name</li>

       <li>street number</li>

       <li>5-digit postal code</li>

       <li>postal area</li>
   </ul>

   <p>A Finnish address looks like this:<br /></p>

   <address>Koulu.katu 9<br />95400 TORNIO</address>

   <p></p>

   <p>The name of the street often ends in the word <i>katu</i> (meaning <i>a street</i>) or <i>tie</i> (<i>a road</i>), but there are lots of other possibilities as well. The postal area is often capitalized. It is usually the name of the city or the town, but not necessarily.</p>

   <p>Make sure you know your own address once in Finland.</p>
</section>

<section class="exercise">
   <h2>Exercise</h2>

   <p>Lue osoitteet ääneen.</p>

   <p>Read the addresses out loud.</p>

   <ol>
       <li>
           <address>Purolan.katu 3<br />94200 Kemi</address>

           <audio controls preload="none" src="/audio/u1e15i1.mp3"></audio>
       </li>

       <li>
           <address>Hatakan.tie 60<br />04920 Saaren.taus</address>

           <audio controls preload="none" src="/audio/u1e15i2.mp3"></audio>
       </li>

       <li>
           <address>Puna.vuoren.katu 12<br />00150 Helsinki</address>

           <audio controls preload="none" src="/audio/u1e15i3.mp3"></audio>
       </li>

       <li>
           <address>Pitkä.tie 223<br />15560 Nastola</address>

           <audio controls preload="none" src="/audio/u1e15i4.mp3"></audio>
       </li>

       <li>
           <address>Daalin.tie 15<br />04360 Tuusula</address>

           <audio controls preload="none" src="/audio/u1e15i5.mp3"></audio>
       </li>

       <li>
           <address>Autio.mäen.tie 149<br />42560 Pohjois.järvi</address>

           <audio controls preload="none" src="/audio/u1e15i6.mp3"></audio>
       </li>
   </ol>
</section>

<section class="exercise">
   <h2>Exercise</h2>

   <p>Kuuntele kadun numerot.</p>

   <p>Listen to the clips and write down the street numbers you hear. The name of the street is already given for clarity. In Finnish addresses, the number goes after the street name.</p>

   <audio controls preload="none" src="/audio/u1e16.mp3"></audio>

   <ol class='printtiviivat'>
       <li>Valta.katu ?</li>

       <li>Karjalan.katu ?</li>

       <li>Haukitie ?</li>

       <li>Vapaudenkatu ?</li>

       <li>Vanhatie ?</li>

       <li>Harjutie ?</li>

       <li>Asematie ?</li>

       <li>Katajarannantie ?</li>

       <li>Maaseläntie ?</li>

       <li>Metsäkatu ?</li>
   </ol>

   <details>
       <summary>Check answers</summary>

       <ol>
           <li>Valta.katu 10</li>

           <li>Karjalan.katu 42</li>

           <li>Haukitie 60</li>

           <li>Vapaudenkatu 4</li>

           <li>Vanhatie 44</li>

           <li>Harjutie 105</li>

           <li>Asematie 21</li>

           <li>Katajarannantie 8</li>

           <li>Maaseläntie 204</li>

           <li>Metsäkatu 19</li>
       </ol>
   </details>
</section>

<section class="exercise">
   <h2>Exercise</h2>

   <p>Kuuntele postinumerot.</p>

   <p>Listen to the clips and write down the postal codes you hear. Each has five digits. The postal area in each clip is already given for clarity.</p>

   <ol class='printtiviivat'>
       <li>? Lappeenranta

           <audio controls preload="none" src="/audio/u1e17i1.mp3"></audio>
       </li>

       <li>? Turku

           <audio controls preload="none" src="/audio/u1e17i2.mp3"></audio>
       </li>

       <li>? Espoo

           <audio controls preload="none" src="/audio/u1e17i3.mp3"></audio>
       </li>

       <li>? Kuhmo

           <audio controls preload="none" src="/audio/u1e17i4.mp3"></audio>
       </li>

       <li>? Ylä-Kolkki

           <audio controls preload="none" src="/audio/u1e17i5.mp3"></audio>
       </li>

       <li>? Kiviniemi

           <audio controls preload="none" src="/audio/u1e17i6.mp3"></audio>
       </li>
   </ol>

   <details>
       <summary>Check answers</summary>
       <ol>
           <li>53100 Lappeenranta</li>
   
           <li>20240 Turku</li>
   
           <li>02940 Espoo</li>
   
           <li>88900 Kuhmo</li>
   
           <li>42830 Ylä-Kolkki</li>
   
           <li>90810 Kiviniemi</li>
       </ol>
   </details>
</section>
HTML
            ],
            ['title' => 'Pronunciation', 'sort' => 8, 'description' => 'Pronounce loan words correctly.', 'slug' => 'u1-pronunciation', 
            'content' => <<<'HTML'
<section>
    
    <p>Finnish has quite a few loan words that resemble words that you might already know from English. Unless the
        word
        is a direct loan from English or a name, it is unlikely that you pronounce it like the English word. Here
        are some
        common words that you should pronounce the Finnish way.</p>

    <audio controls preload="none" src="/audio/u1-pronunciation-loan-words.mp3">u1-pronunciation-loan-words</audio>

    <table class="sanasto">
        <caption></caption>

        <thead>
            <tr>
                <th>
                    Finnish
                </th>

                <th>
                    English
                </th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    antibiootti
                </td>

                <td>
                    an antibiotic
                </td>
            </tr>

            <tr>
                <td>
                    ambulanssi
                </td>

                <td>
                    an ambulance
                </td>
            </tr>

            <tr>
                <td>
                    bio.jäte
                </td>

                <td>
                    biowaste
                </td>
            </tr>

            <tr>
                <td>
                    diabetes
                </td>

                <td>
                    diabetes
                </td>
            </tr>

            <tr>
                <td>
                    dialyysi
                </td>

                <td>
                    dialysis
                </td>
            </tr>

            <tr>
                <td>
                    hypotermia
                </td>

                <td>
                    hypothermia
                </td>
            </tr>

            <tr>
                <td>
                    infuusio
                </td>

                <td>
                    infusion
                </td>
            </tr>

            <tr>
                <td>
                    migreeni
                </td>

                <td>
                    migraine
                </td>
            </tr>

            <tr>
                <td>
                    neurologi
                </td>

                <td>
                    a neurologist
                </td>
            </tr>

            <tr>
                <td>
                    psykologi
                </td>

                <td>
                    a psychologist
                </td>
            </tr>

            <tr>
                <td>
                    sedaatio
                </td>

                <td>
                    sedation
                </td>
            </tr>
        </tbody>
    </table>
</section>
HTML
            ],
            ['title' => 'Review', 'sort' => 9, 'description' => 'Finish the exercise to review things from the beginners\' course.', 'slug' => 'u1-review', 
            'content' => <<<'HTML'
 <section class="exercise">
            <h2>Exercise</h2>
    
            <p>Lue dialogi. Muuta sen jälkeen dialogia.</p>
    
            <p>Read the phone conversation out loud. Then apply some of the changes listed below on the fly. The speakers are labeled as Ä and Ö.</p>
    
            <ul class="dialogi puhujat exercise">
                <li>Moi.</li>
    
                <li>No moi.</li>
    
                <li>Missä sä oot?</li>
    
                <li>Töissä.</li>
    
                <li>Missä Laura on?</li>
    
                <li>En tiiä.</li>
    
                <li>Se ei vastaa mun viestiin.</li>
    
                <li>Ai jaa...</li>
    
                <li>Ehkä se on suihkussa.</li>
            </ul>
    
            <audio controls preload="none" src="/audio/u1e18.mp3"></audio>
    
            <ol class="muutettavat">
                <li>Change the greetings to what the speakers could say
    
                    <ol>
                        <li>in the morning</li>
    
                        <li>in the evening</li>
                    </ol>
                </li>
    
                <li>Ö is...
    
                    <ol>
                        <li>on the bus (<i>bussi</i>)</li>
    
                        <li>on a train (<i>juna</i>)</li>
    
                        <li>in Mikkeli (a city in Finland)</li>
    
                        <li>at home (<i>koti</i> : <i>koto<span class="ala">na</span></i>, no -<i>ssA</i>)</li>
                    </ol>
                </li>
    
                <li>Ä is looking for a friend called...
    
                    <ol>
                        <li>Matias</li>
    
                        <li>Anni</li>
    
                        <li>Virve</li>
    
                        <li>Heli</li>
                    </ol>
                </li>
    
                <li>The person is probably...
    
                    <ol>
                        <li>at work</li>
    
                        <li>at McDonald's</li>
    
                        <li>in Kotka (a city in Finland)</li>
    
                        <li>on vacation (<i>loma</i> : <i>loma<span class="ala">lla</span></i>)</li>
                    </ol>
                </li>
            </ol>
    
            <details>
                <summary>Check translation</summary>

                <ul class="dialogi">
                    <li>Hi.</li>
            
                    <li>Oh hi.</li>
            
                    <li>Where are you?</li>
            
                    <li>At work.</li>
            
                    <li>Where's Laura?</li>
            
                    <li>I don't know.</li>
            
                    <li>She's not replying to my message.</li>
            
                    <li>Oh...</li>
            
                    <li>Maybe she's in the shower.</li>
                </ul>
            </details>
        </section>
HTML
            ],
            ['title' => 'Question words', 'sort' => 10, 'description' => 'Where and what.', 'slug' => 'u1-question-words', 
            'content' => <<<'HTML'
<section>   
            <p>If you studied the entire <i>Kyl mä hoidan</i> beginners' course, you should know these question words.
                Memorize
                them if they seem unfamiliar.</p>
    
            <table class="sanasto">
                <caption></caption>
    
                <thead>
                    <tr>
                        <th>
                            Finnish
                        </th>
    
                        <th>
                            English
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <td>
                            missä?
                        </td>
    
                        <td>
                            <span class="norm">[</span>in<span class="norm">]</span> where?<br />
    
                            <span class="norm">(answer with</span> ‑ssA<span class="norm">)</span>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            mistä?
                        </td>
    
                        <td>
                            from where?<br />
    
                            <span class="norm">(answer with</span> -stA<span class="norm">)</span>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            mikä? ~ mitä?
                        </td>
    
                        <td>
                            what?
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            millainen? ~ millaista?
                        </td>
    
                        <td>
                            what kind of?
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            monelta?
                        </td>
    
                        <td>
                            <span class="norm">[</span>at<span class="norm">]</span> what time?
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <p id='u1-kysymys-jarj'>Adding the question word does not change the word order in Finnish. In English, using a question word requires an auxiliary verb like <i>do</i> (e.g. <i>where does this go?</i>) or makes you move the <i>to be</i> verb after the question word (e.g. <i>where is it?</i>). In Finnish, questions with a question word use the same word order as statements. Just put the question word before everything else.</p>
    
            <table class="esimerkki">
                <caption>questions that use a question word</caption>
    
                <tbody>
                    <tr>
                        <td>
                            Missä sä asut?
                        </td>
    
                        <td>
                            Where do you live?<br />
    
                            <span class="norm">lit.</span> Where you live?
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            Mistä sä oot?
                        </td>
    
                        <td>
                            Where are you from?
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            Millanen sää siel on?
                        </td>
    
                        <td>
                            What's the weather like?
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            Mikä toi on?
                        </td>
    
                        <td>
                            What is that?
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    
        <section class="exercise" id="u1-tee-kysymys">
            <h2>Exercise</h2>
    
            <p>Sano suomeksi.</p>
    
            <p>Express a similar idea in Finnish.</p>
    
            <ol class='printtiviivat'>
                <li>Where are you<sup>one person</sup>?</li>
    
                <li>Where is Miia<sup>name</sup>?</li>
    
                <li>Where do you<sup>more than one person</sup> live?</li>
    
                <li>Where am I?</li>
    
                <li>Where is he from?</li>
    
                <li>What is this?</li>
    
                <li>Where are we?</li>
    
                <li>Where is my phone (<i>puhelin</i>)?</li>
            </ol>
    
            <details>
                <summary>Check answers</summary>
                
                <audio controls preload="none" src="/audio/u1e19.mp3"></audio>

                <ol>
                    <li>Missä sä oot?</li>
            
                    <li>Missä Miia on?</li>
            
                    <li>Missä te asutte?</li>
            
                    <li>Missä mä oon?</li>
            
                    <li>Mistä se on (kotoisin)?</li>
            
                    <li>Mikä tää on? ~ Mitä tää on?</li>
            
                    <li>Missä me ollaan?</li>
            
                    <li>Missä mun puhelin on?</li>
                </ol>
            </details>
        </section>
HTML
            ],
        ];

        foreach ($topicsForUnit1 as $topicData) {
            Topic::create(array_merge($topicData, ['unit_id' => $unit1->id]));
        }

        // Create remaining units for courses (except for the beginners course for now)
        $sort = 2;
        $mainUnits = Unit::factory()->count(19)->create(['course_id' => $main->id, 'sort' => function() use (&$sort) {
            return $sort++;
        }]);

        $sort = 1;
        $nmhUnits = Unit::factory()->count(8)->create(['course_id' => $nmh->id, 'sort' => function() use (&$sort) {
            return $sort++;
        }]);

        $units = $mainUnits->concat($nmhUnits);

        // Then add topics to each unit
        $units->each(function ($unit) {
            $sort = 1;
            Topic::factory()->count(rand(4, 6))->create(['unit_id' => $unit->id])->each(function ($topic) use (&$sort) {
                $topic->sort = $sort++;
                $topic->save();
            });
        });
    }
}
