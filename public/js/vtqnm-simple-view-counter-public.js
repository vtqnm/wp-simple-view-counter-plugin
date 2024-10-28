(function( $ ) {
	'use strict';

	if (!window.hasOwnProperty('Vtqnm')) {
		window.Vtqnm = {};
	}

	window.Vtqnm.PostViews = class {
		constructor(settings) {
			this.settings = settings;
		}

		async markViewed(id) {
			if (this.isAlreadyViewed(id)) {
				return true;
			}

			const form = new FormData();
			form.append('nonce', this.settings.nonce);
			form.append('action', 'update_views_counter')
			form.append('post_id', id)

			fetch(this.settings.url, {
				method: 'post',
				body: form
			}).then(result => {
				this.addViewedPost(id)
			})
		}

		markViewedAfterDelay(id, delay = 0) {
			if (!Number.isInteger(delay) || delay < 0) {
				delay = 0;
			}

			setTimeout(() => {
				this.markViewed(id);
			}, delay * 1000)
		}

		isAlreadyViewed(id) {
			return this.getViewedPosts().includes(id);
		}

		getViewedPosts() {
			try {
				const data = JSON.parse(localStorage.getItem("viewedPosts"));
				return Array.isArray(data) ? data : [];

			} catch (error) {
				return [];
			}
		}

		addViewedPost(postId) {
			const posts = this.getViewedPosts();
			posts.push(postId)

			this.setViewedPosts(posts);
		}

		setViewedPosts(viewedPosts) {
			localStorage.setItem('viewedPosts', JSON.stringify(viewedPosts));
		}

		rememberPostId(id) {
			let viewedPosts = JSON.parse(localStorage.getItem("viewedPosts")) || [];
			if (!viewedPosts.includes(postId)) {
				viewedPosts.push(postId);
				localStorage.setItem("viewedPosts", JSON.stringify(viewedPosts));
			}
		}

	}

})( jQuery );
