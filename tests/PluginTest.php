<?php

use WildWolf\WordPress\CyrToLat;

class PluginTest extends WP_UnitTestCase
{
    public function testDefaults()
    {
        $expected = [
            'posts' => 0,
            'terms' => 0,
            'files' => 0
        ];

        $actual = get_option(CyrToLat::OPTIONS_KEY);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider postsUkDataProvider
     */
    public function testPosts_uk($title, $expected)
    {
        global $locale;
        $locale = 'uk';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['posts'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id   = $this->factory->post->create(['post_title' => $title]);
        $post = get_post($id);

        $this->assertEquals($expected, $post->post_name);
    }

    function postsUkDataProvider()
    {
        return [
            ['Тест',       'test'],
            ['ялинка',     'yalynka'],
            ['моя ялинка', 'moia-yalynka'],
            ['століття',   'stolittia'],
            ["кон'юктурa", 'koniuktura'],
            ["люкс",       'liuks'],
            ["Юля",        'yulia'],
            ["йолка",      'yolka'],
            ["койот",      'koiot'],
            ["Київ",       'kyiv'],
            ['їжа',        'yizha'],
            ['Згораны',    'zghorany'],
            ['Євген',      'yevhen'],
            ['барельєф',   'barelief'],
        ];
    }

    /**
     * @dataProvider postsRuDataProvider
     */
    public function testPosts_ru($title, $expected)
    {
        global $locale;
        $locale = 'ru_RU';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['posts'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id   = $this->factory->post->create(['post_title' => $title]);
        $post = get_post($id);

        $this->assertEquals($expected, $post->post_name);
    }

    public function postsRuDataProvider()
    {
        return [
            ['вчера приснился сон прекрасный', 'vchera-prisnilsya-son-prekrasnyj'],
            ['Москва сгорела целиком',         'moskva-sgorela-celikom'],
            ['пожар на площади на красной',    'pozhar-na-ploshhadi-na-krasnoj'],
            ['и тлеет бывший избирком',        'i-tleet-byvshij-izbirkom'],
        ];
    }

    /**
     * @dataProvider postsBeDataProvider
     */
    function testPosts_be($title, $expected)
    {
        global $locale;
        $locale = 'be';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['posts'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id   = $this->factory->post->create(['post_title' => $title]);
        $post = get_post($id);

        $this->assertEquals($expected, $post->post_name);
    }

    public function postsBeDataProvider()
    {
        return [
            ['учора прысніўся сон выдатны',    'uchora-prysniusya-son-vydatny'],
            ['Масква згарэла цалкам',          'maskva-zharela-calkam'],
        ];
    }

    public function testConflictResolutionPosts()
    {
        global $locale;
        $locale = 'uk';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['posts'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id1 = $this->factory->post->create(['post_title' => 'Тестова запись']);
        $id2 = $this->factory->post->create(['post_title' => 'Тестова запись']);
        $id3 = $this->factory->post->create(['post_title' => 'Тестова запись']);

        $post1 = get_post($id1);
        $post2 = get_post($id2);
        $post3 = get_post($id3);

        $this->assertEquals('testova-zapys',   $post1->post_name);
        $this->assertEquals('testova-zapys-2', $post2->post_name);
        $this->assertEquals('testova-zapys-3', $post3->post_name);
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testTags($tag, $expected)
    {
        global $locale;
        $locale = 'uk';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['terms'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id  = $this->factory->tag->create(['name' => $tag]);
        $tag = get_tag($id);

        $this->assertEquals($expected, $tag->slug);
    }

    public function tagsProvider()
    {
        return [
            ['абвгдеёжзийк', 'abvhdeyozhzyik'],
            ['лмнопрстуфхц', 'lmnoprstufkhts'],
            ['чшщъыьэюяєїґ', 'chshshchyeiuiaieig'],
        ];
    }

    public function testConflictResolutionTags()
    {
        global $locale;
        $locale = 'uk';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['terms'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $id1 = $this->factory->tag->create(['name' => 'Тестова позначка']);
        $id2 = $this->factory->tag->create(['name' => 'Тестова позначка!']);
        $id3 = $this->factory->tag->create(['name' => 'Тестова позначка?']);

        $tag1 = get_tag($id1);
        $tag2 = get_tag($id2);
        $tag3 = get_tag($id3);

        $this->assertEquals('testova-poznachka',   $tag1->slug);
        $this->assertEquals('testova-poznachka-2', $tag2->slug);
        $this->assertEquals('testova-poznachka-3', $tag3->slug);
    }

    public function testFiles()
    {
        global $locale;
        $locale = 'uk';

        $options = get_option(CyrToLat::OPTIONS_KEY);
        $options['posts'] = 1;
        $options['files'] = 1;
        update_option(CyrToLat::OPTIONS_KEY, $options);

        CyrToLat::instance()->reinstallHooks();

        $pid = $this->factory->post->create(['post_title' => 'Test']);
        $aid = $this->factory->attachment->create(['post_parent' => $pid, 'file' => '/' . sanitize_file_name('файл.jpg'), 'post_title' => 'файл']);

        $post = get_post($aid);
        $this->assertEquals('fail', $post->post_name);

        $file = get_attached_file($aid, true);
        $this->assertEquals('/fail.jpg', $file);
    }
}
