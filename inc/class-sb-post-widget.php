<?php
class SB_Post_Widget extends WP_Widget {
	public $types = array();
	private $order_by = array();
	private $order_type = array();
	
	private $default_number = 5;
	private $excerpt_length = 75;
	private $thumbnail_size = array(100, 70);
    private $title_length = 50;
    private $show_author = 0;
    private $show_date = 0;
    private $show_comment_count = 0;

	public function __construct() {
		$this->init();
	
		parent::__construct( 'sb_post_widget', 'SB Post', array(
			'classname'   => 'widget_sb_post',
			'description' => __( 'Show custom post on sidebar.', 'sb-post-widget' ),
		));
	}
	
	private function init() {
		$this->type_init();
		$this->order_by_init();
		$this->order_type_init();
	}
	
	private function type_init() {
		$this->types = array(
			'recent'	=> __('Recent posts', 'sb-post-widget'),
			'random'	=> __('Random posts', 'sb-post-widget'),
			'comment'	=> __('Most comment posts', 'sb-post-widget'),
			'view'		=> __('Most view posts', 'sb-post-widget'),
			'like'		=> __('Most like posts', 'sb-post-widget'),
			'category'	=> __('Post by category', 'sb-post-widget'),
			'favorite'	=> __('Favorite posts', 'sb-post-widget')
		);
	}
	
	private function order_type_init() {
		$this->order_type = array(
			'desc'	=> __('DESC', 'sb-post-widget'),
			'asc'	=> __('ASC', 'sb-post-widget')
		);
	}
	
	private function order_by_init() {
		$this->order_by = array(
			'title'		=> __('Title', 'sb-post-widget'),
			'post_date'	=> __('Post date', 'sb-post-widget')
		);
	}
	
	public function widget($args, $instance) {
		$arr_tmp = $args;
		$number = empty( $instance['number'] ) ? $this->default_number : absint( $instance['number'] );
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$type = isset($instance['type']) ? $instance['type'] : 'recent';
		$taxonomy = isset($instance['taxonomy']) ? $instance['taxonomy'] : 'category';
		$order_by = isset($instance['order_by']) ? $instance['order_by'] : 'title';
		$order_type = $instance['order_type'];
		$order_type = strtoupper($order_type);

		$only_thumbnail = isset($instance['only_thumbnail']) ? absint($instance['only_thumbnail']) : 0;
		$show_excerpt = isset($instance['show_excerpt']) ? absint($instance['show_excerpt']) : 0;
		
		$thumbnail_width = empty( $instance['thumbnail_width'] ) ? $this->thumbnail_size[0] : absint( $instance['thumbnail_width'] );
		$thumbnail_height = empty( $instance['thumbnail_height'] ) ? $this->thumbnail_size[1] : absint( $instance['thumbnail_height'] );
		$thumbnail_size = array($thumbnail_width, $thumbnail_height);
		
		$excerpt_length = empty( $instance['excerpt_length'] ) ? $this->excerpt_length : absint( $instance['excerpt_length'] );
		
		$show_author = isset($instance['show_author']) ? absint($instance['show_author']) : 0;
		$show_date = isset($instance['show_date']) ? absint($instance['show_date']) : 0;
		$show_comment_count = isset($instance['show_comment_count']) ? absint($instance['show_comment_count']) : 0;

        $title_length = empty( $instance['title_length'] ) ? $this->title_length : absint( $instance['title_length'] );
		switch($type) {
			case 'random':
				$args = array(
					'posts_per_page'	=> $number,
					'orderby'			=> 'rand',
					'order'				=> $order_type
				);
				break;
			case 'comment':
				$args = array(
					'posts_per_page'	=> $number,
					'orderby'			=> 'comment_count',
					'order'				=> $order_type
				);
				break;
			case 'view':
				$args = array(
					'posts_per_page'	=> $number,
					'meta_key'			=> 'views',
					'orderby'			=> 'meta_value_num',
					'order'				=> $order_type
				);
				break;
			case 'like':
				$args = array(
					'posts_per_page'	=> $number,
					'meta_key'			=> 'likes',
					'orderby'			=> 'meta_value_num',
					'order'				=> $order_type
				);
				break;
			case 'favorite':
				$user = wp_get_current_user();
				$list_posts = array();
				if(!empty($user)) {
					$list_posts = (array)get_user_meta($user->ID, 'favorite_posts', true);
				}				
				if(count($list_posts) < 1) {
					array_push($list_posts, 0);
				}
				$args = array(
					'posts_per_page'	=> $number,
					'post__in'			=> $list_posts,
					'orderby'			=> $order_by,
					'order'				=> $order_type
				);
				break;
			case 'category':
				$args = array();
				$category = $instance['category'];
				if($category > 0) {
					$args = array(
						'posts_per_page'	=> $number,
						'orderby'			=> $order_by,
						'order'				=> $order_type,
						'tax_query'		=> array(
							array(
								'taxonomy'	=> $taxonomy,
								'field'		=> 'id',
								'terms'		=> $category
							)
						)
					);
				}
				break;
			default:
				$args = array(
					'posts_per_page'	=> $number,
					'orderby'			=> $order_by,
					'order'				=> $order_type
				);
		}

		$sb_post = new WP_Query($args);

		if($sb_post->have_posts()) {
			if('favorite' == $type && !is_user_logged_in()) return;
			$args = $arr_tmp;
			echo $args['before_widget'];
			if(!empty($title)) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			?>
			<div class='sb-post-widget'>
				<div class='sb-post-widget-inner'>
					<ol class='list-unstyled list-posts'>
						<?php while($sb_post->have_posts()) : $sb_post->the_post(); ?>
                            <li>
                                <?php SB_Post::the_thumbnail_html(array('size' => $thumbnail_size, 'post_id' => get_the_ID())); ?>
                                <?php if(!$only_thumbnail) : ?>
                                    <h3 class='post-title'><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h3>
                                    <?php if((bool)$show_date) : ?>
                                        <span class='date'><?php SB_post::the_date(); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
						<?php endwhile; wp_reset_postdata(); ?>
					</ol>
				</div>
			</div>
			<?php
			echo $args['after_widget'];
		}
	}
	
	public function form($instance) {
		$title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$number = empty( $instance['number'] ) ? $this->default_number : absint( $instance['number'] );
		$type = isset( $instance['type'] ) ? $instance['type'] : 'recent';
		$category = isset( $instance['category'] ) ? $instance['category'] : 0;
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'category';
		$only_thumbnail = isset($instance['only_thumbnail']) ? absint($instance['only_thumbnail']) : 0;
		$show_excerpt = isset($instance['show_excerpt']) ? absint($instance['show_excerpt']) : 0;
		$order_by = isset( $instance['order_by'] ) ? $instance['order_by'] : 'title';
		$order_type = isset($instance['order_type']) ? $instance['order_type'] : 'desc';
		
		$thumbnail_width = empty( $instance['thumbnail_width'] ) ? $this->thumbnail_size[0] : absint( $instance['thumbnail_width'] );
		$thumbnail_height = empty( $instance['thumbnail_height'] ) ? $this->thumbnail_size[1] : absint( $instance['thumbnail_height'] );
		$thumbnail_size = array($thumbnail_width, $thumbnail_height);
		
		$excerpt_length = empty( $instance['excerpt_length'] ) ? $this->excerpt_length : absint( $instance['excerpt_length'] );
        $title_length = empty( $instance['title_length'] ) ? $this->title_length : absint( $instance['title_length'] );

		$show_author = isset($instance['show_author']) ? absint($instance['show_author']) : 0;
        $this->show_author = $show_author;
		$show_date = isset($instance['show_date']) ? absint($instance['show_date']) : 0;
        $this->show_date = $show_date;
		$show_comment_count = isset($instance['show_comment_count']) ? absint($instance['show_comment_count']) : 0;
        $this->show_comment_count = $show_comment_count;
        if($only_thumbnail) {
            $show_excerpt = false;
        }
		?>
		<div class='sb-post-widget sb-widget'>
			<?php
            $args = array(
                'id'            => $this->get_field_id( 'title' ),
                'name'          => $this->get_field_name( 'title' ),
                'value'         => $title,
                'label_text'    => __('Title:', 'sb-post-widget'),
                'description'   => ''
            );
            SB_Widget_Field::text($args);

			$args = array(
				'id'				=> $this->get_field_id('number'),
				'name'				=> $this->get_field_name('number'),
				'value'				=> $number,
                'label_text'        => __('Post number:', 'sb-post-widget'),
				'description'		=> __('The number of posts to be displayed.', 'sb-post-widget'),
				'paragraph_id'		=> 'postNumber',
				'paragraph_class'	=> 'post-number'
			);
            SB_Widget_Field::number($args);

            $args = array(
                'id'            => $this->get_field_id( 'type' ),
                'name'          => $this->get_field_name( 'type' ),
                'value'         => $type,
                'label_text'    => __('Get post by:', 'sb-post-widget'),
                'list_options'  => $this->types,
                'field_class'   => 'sb-post-type',
                'description'   => __('Choose the way you want to get post.', 'sb-post-widget'),
                'paragraph_class' => 'post-type'
            );
            SB_Widget_Field::select($args);

            $taxs = SB_Core::get_all_taxonomy_hierarchical();
			if($taxs) {

                $args = array(
                    'id' => $this->get_field_id('category'),
                    'name' => $this->get_field_name('category'),
                    'value' => $category,
                    'label_text' => __('Choose category:', 'sb-post-widget'),
                    'list_options' => $taxs,
                    'field_class' => 'widefat',
                    'paragraph_class' => 'post-cat',
                    'taxonomy' => $taxonomy,
                    'taxonomy_id' => $this->get_field_id('taxonomy'),
                    'taxonomy_name' => $this->get_field_name('taxonomy'),
                    'display' => ($type == 'category') ? true : false
                );
                SB_Widget_Field::select_term($args);
            }

			$args = array(
				'id'				=> $this->get_field_id('only_thumbnail'),
				'name'				=> $this->get_field_name('only_thumbnail'),
				'value'				=> $only_thumbnail,
				'label_text'		=> __('Show only thumbnail', 'sb-post-widget'),
				'paragraph_id'		=> 'onlyThumbnail',
				'paragraph_class'	=> 'only-thumbnail'
			);
            SB_Widget_Field::checkbox($args);

			$args = array(
				'id_width'			=> $this->get_field_id('thumbnail_width'),
				'name_width'		=> $this->get_field_name('thumbnail_width'),
				'id_height'			=> $this->get_field_id('thumbnail_height'),
				'name_height'		=> $this->get_field_name('thumbnail_height'),
                'label_text'        => __('Image size:', 'sb-post-widget'),
				'value'				=> $thumbnail_size,
				'description'		=> '',
				'paragraph_id'		=> 'thumbnailSize',
				'paragraph_class'	=> 'thumbnail-size'
			);
            SB_Widget_Field::size($args);

			$args = array(
				'id'				=> $this->get_field_id('show_excerpt'),
				'name'				=> $this->get_field_name('show_excerpt'),
				'value'				=> $show_excerpt,
				'label_text'		=> __('Show excerpt:', 'sb-post-widget'),
				'paragraph_id'		=> 'showExcerpt',
				'paragraph_class'	=> 'show-excerpt',
                'display' => ((bool)$only_thumbnail) ? false : true
			);
            SB_Widget_Field::checkbox($args);

			$args = array(
				'id'				=> $this->get_field_id('excerpt_length'),
				'name'				=> $this->get_field_name('excerpt_length'),
				'value'				=> $excerpt_length,
                'label_text'        => __('Excerpt length:', 'sb-post-widget'),
				'description'		=> '',
				'paragraph_id'		=> 'excerptLength',
				'display'			=> ((bool)$show_excerpt) ? true : false,
				'paragraph_class'	=> 'excerpt-length'
			);
            SB_Widget_Field::number($args);

            $args = array(
                'id'				=> $this->get_field_id('title_length'),
                'name'				=> $this->get_field_name('title_length'),
                'value'				=> $title_length,
                'label_text'		=> __('Title length:', 'sb-post-widget'),
                'paragraph_id'		=> 'titleLength',
                'display' => ((bool)$only_thumbnail) ? false : true,
                'paragraph_class'	=> 'title-length'
            );
            SB_Widget_Field::number($args);

            $args = array(
                'id' => $this->get_field_id('order_by'),
                'name' => $this->get_field_name('order_by'),
                'value' => $order_by,
                'label_text' => __('Order by:', 'sb-post-widget'),
                'list_options' => $this->order_by,
                'field_class' => 'widefat',
                'paragraph_class' => 'order-by'
            );
            SB_Widget_Field::select($args);

            $args = array(
                'id' => $this->get_field_id('order_type'),
                'name' => $this->get_field_name('order_type'),
                'value' => $order_type,
                'label_text' => __('Order type:', 'sb-post-widget'),
                'list_options' => $this->order_type,
                'field_class' => 'widefat',
                'paragraph_class' => 'order-by'
            );
            SB_Widget_Field::select($args);

            $args = array(
                'title' => __('Post information', 'sb-post-widget'),
                'callback' => array($this, 'sb_post_information'),
                'class' => 'post-info',
                'display' => ((bool)$only_thumbnail) ? false : true
            );
            SB_Widget_Field::fieldset($args);
            ?>
			
		</div>
		<?php
	}

    public function sb_post_information() {
        $args = array(
            'id'				=> $this->get_field_id('show_author'),
            'name'				=> $this->get_field_name('show_author'),
            'value'				=> $this->show_author,
            'label_text'		=> __('Show author', 'sb-post-widget'),
            'paragraph_id'		=> 'showAuthor',
            'paragraph_class'	=> 'show-author'
        );
        SB_Widget_Field::checkbox($args);

        $args = array(
            'id'				=> $this->get_field_id('show_date'),
            'name'				=> $this->get_field_name('show_date'),
            'value'				=> $this->show_date,
            'label_text'		=> __('Show date', 'sb-post-widget'),
            'paragraph_class'	=> 'show-date'
        );
        SB_Widget_Field::checkbox($args);

        $args = array(
            'id'				=> $this->get_field_id('show_comment_count'),
            'name'				=> $this->get_field_name('show_comment_count'),
            'value'				=> $this->show_comment_count,
            'label_text'		=> __('Show comment count', 'sb-post-widget'),
            'paragraph_class'	=> 'show-comment-count'
        );
        SB_Widget_Field::checkbox($args);
    }

	public function update($new_instance, $instance) {
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags( $new_instance['title'] ) : '';
		$instance['type'] = $new_instance['type'];
		$instance['category'] = $new_instance['category'];
		$instance['number'] = empty( $new_instance['number'] ) ? $this->default_number : absint( $new_instance['number'] );
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['only_thumbnail'] = isset($new_instance['only_thumbnail']) ? 1 : 0;
		$instance['show_excerpt'] = isset($new_instance['show_excerpt']) ? 1 : 0;
		
		$instance['show_author'] = isset($new_instance['show_author']) ? 1 : 0;
		$instance['show_date'] = isset($new_instance['show_date']) ? 1 : 0;
		$instance['show_comment_count'] = isset($new_instance['show_comment_count']) ? 1 : 0;
		
		$instance['order_by'] = $new_instance['order_by'];
		$instance['order_type'] = $new_instance['order_type'];
		
		$instance['thumbnail_width'] = empty( $new_instance['thumbnail_width'] ) ? $this->thumbnail_size[0] : absint( $new_instance['thumbnail_width'] );
		$instance['thumbnail_height'] = empty( $new_instance['thumbnail_height'] ) ? $this->thumbnail_size[1] : absint( $new_instance['thumbnail_height'] );
		
		$instance['excerpt_length'] = empty( $new_instance['excerpt_length'] ) ? $this->excerpt_length : absint( $new_instance['excerpt_length'] );
        $instance['title_length'] = empty( $new_instance['title_length'] ) ? $this->title_length : absint( $new_instance['title_length'] );
		return $instance;
	}
}
?>