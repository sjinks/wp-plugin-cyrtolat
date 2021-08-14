<?php

use WildWolf\WordPress\CyrToLat\Plugin;

class Test_Plugin extends WP_UnitTestCase {

	public function testDefaults(): void {
		$expected = [
			'posts' => 0,
			'terms' => 0,
			'files' => 0,
		];

		/** @var mixed */
		$actual = get_option( Plugin::OPTIONS_KEY );
		self::assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider postsUkDataProvider
	 */
	public function testPosts_uk( string $title, string $expected ): void {
		global $locale;
		$locale = 'uk';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['posts'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id   = $this->factory->post->create( [ 'post_title' => $title ] );
		$post = get_post( $id );

		self::assertInstanceOf( WP_Post::class, $post );
		self::assertEquals( $expected, $post->post_name );
	}

	/**
	 * @psalm-return iterable<array-key, array{string, string}>
	 */
	public function postsUkDataProvider(): iterable {
		return [
			[ 'Тест', 'test' ],
			[ 'ялинка', 'yalynka' ],
			[ 'моя ялинка', 'moia-yalynka' ],
			[ 'століття', 'stolittia' ],
			[ "кон'юктурa", 'koniuktura' ],
			[ 'люкс', 'liuks' ],
			[ 'Юля', 'yulia' ],
			[ 'йолка', 'yolka' ],
			[ 'койот', 'koiot' ],
			[ 'Київ', 'kyiv' ],
			[ 'їжа', 'yizha' ],
			[ 'Згорани', 'zghorany' ],
			[ 'Євген', 'yevhen' ],
			[ 'барельєф', 'barelief' ],
		];
	}

	/**
	 * @dataProvider postsRuDataProvider
	 */
	public function testPosts_ru( string $title, string $expected ): void {
		global $locale;
		$locale = 'ru_RU';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['posts'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id   = $this->factory->post->create( [ 'post_title' => $title ] );
		$post = get_post( $id );

		self::assertInstanceOf( WP_Post::class, $post );
		self::assertEquals( $expected, $post->post_name );
	}

	/**
	 * @psalm-return iterable<array-key, array{string, string}>
	 */
	public function postsRuDataProvider(): iterable {
		return [
			[ 'вчера приснился сон прекрасный', 'vchera-prisnilsya-son-prekrasnyj' ],
			[ 'Москва сгорела целиком', 'moskva-sgorela-celikom' ],
			[ 'пожар на площади на красной', 'pozhar-na-ploshhadi-na-krasnoj' ],
			[ 'и тлеет бывший избирком', 'i-tleet-byvshij-izbirkom' ],
		];
	}

	/**
	 * @dataProvider postsBeDataProvider
	 */
	public function testPosts_be( string $title, string $expected ): void {
		global $locale;
		$locale = 'be';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['posts'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id   = $this->factory->post->create( [ 'post_title' => $title ] );
		$post = get_post( $id );

		self::assertInstanceOf( WP_Post::class, $post );
		self::assertEquals( $expected, $post->post_name );
	}

	/**
	 * @psalm-return iterable<array-key, array{string, string}>
	 */
	public function postsBeDataProvider(): iterable {
		return [
			[ 'учора прысніўся сон выдатны', 'uchora-prysniusya-son-vydatny' ],
			[ 'Масква згарэла цалкам', 'maskva-zharela-calkam' ],
		];
	}

	public function testConflictResolutionPosts(): void {
		global $locale;
		$locale = 'uk';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['posts'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id1 = $this->factory->post->create( [ 'post_title' => 'Тестова запись' ] );
		$id2 = $this->factory->post->create( [ 'post_title' => 'Тестова запись' ] );
		$id3 = $this->factory->post->create( [ 'post_title' => 'Тестова запись' ] );

		$post1 = get_post( $id1 );
		$post2 = get_post( $id2 );
		$post3 = get_post( $id3 );

		self::assertInstanceOf( WP_Post::class, $post1 );
		self::assertInstanceOf( WP_Post::class, $post2 );
		self::assertInstanceOf( WP_Post::class, $post3 );

		self::assertEquals( 'testova-zapys', $post1->post_name );
		self::assertEquals( 'testova-zapys-2', $post2->post_name );
		self::assertEquals( 'testova-zapys-3', $post3->post_name );
	}

	/**
	 * @dataProvider tagsProvider
	 */
	public function testTags( string $tag, string $expected ): void {
		global $locale;
		$locale = 'uk';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['terms'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id  = $this->factory->tag->create( [ 'name' => $tag ] );
		$tag = get_tag( $id );

		self::assertInstanceOf( WP_Term::class, $tag );
		self::assertEquals( $expected, $tag->slug );
	}

	/**
	 * @psalm-return iterable<array-key, array{string, string}>
	 */
	public function tagsProvider(): iterable {
		return [
			[ 'абвгдеёжзийк', 'abvhdeyozhzyik' ],
			[ 'лмнопрстуфхц', 'lmnoprstufkhts' ],
			[ 'чшщъыьэюяєїґ', 'chshshchyeiuiaieig' ],
		];
	}

	public function testConflictResolutionTags(): void {
		global $locale;
		$locale = 'uk';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['terms'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$id1 = $this->factory->tag->create( [ 'name' => 'Тестова позначка' ] );
		$id2 = $this->factory->tag->create( [ 'name' => 'Тестова позначка!' ] );
		$id3 = $this->factory->tag->create( [ 'name' => 'Тестова позначка?' ] );

		$tag1 = get_tag( $id1 );
		$tag2 = get_tag( $id2 );
		$tag3 = get_tag( $id3 );

		self::assertInstanceOf( WP_Term::class, $tag1 );
		self::assertInstanceOf( WP_Term::class, $tag2 );
		self::assertInstanceOf( WP_Term::class, $tag3 );

		self::assertEquals( 'testova-poznachka', $tag1->slug );
		self::assertEquals( 'testova-poznachka-2', $tag2->slug );
		self::assertEquals( 'testova-poznachka-3', $tag3->slug );
	}

	public function testFiles(): void {
		global $locale;
		$locale = 'uk';

		/** @var array<string,int> */
		$options          = get_option( Plugin::OPTIONS_KEY );
		$options['posts'] = 1;
		$options['files'] = 1;
		update_option( Plugin::OPTIONS_KEY, $options );

		Plugin::instance()->reinstall_hooks();

		$pid = $this->factory->post->create( [ 'post_title' => 'Test' ] );
		$aid = $this->factory->attachment->create(
			[
				'post_parent' => $pid,
				'file'        => '/' . sanitize_file_name( 'файл.jpg' ),
				'post_title'  => 'файл',
			]
		);

		$post = get_post( $aid );
		self::assertInstanceOf( WP_Post::class, $post );
		self::assertEquals( 'fail', $post->post_name );

		$file = get_attached_file( $aid, true );
		self::assertEquals( '/fail.jpg', $file );
	}
}
