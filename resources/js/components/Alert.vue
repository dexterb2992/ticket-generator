<template>
	<div :class="`alert alert-${notification.type} alert-dismissible fade show`" role="alert">
		{{ notification.message }}
		<button type="button" class="close" aria-label="Close" @click="hideAlert">
			<span>&times;</span>
		</button>
	</div>
</template>

<script>
	export default {
		props: ['notification'],
		data() {
			return {
				alertClass: `alert alert-info show`
			};
		},
		mounted() {
			this.alertClass = `alert alert-${notification.type} alert-dismissible fade show`
		},
		methods: {
			hideAlert() {
				Event.fire('hideAlert');
			}
		}
	}
</script>