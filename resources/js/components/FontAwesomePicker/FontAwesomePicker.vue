<template>
<!-- Copied source code by installing module font-awesome-picker@1.1.6-->
	<div class="col-sm-12 m-0 p-0" id="iconPicker">
		<div class="iconPicker__header">
			<input type="text" :placeholder="box_placeholder" @keyup="filterIcons($event)" @keydown.enter.prevent>
		</div>
		<div class="iconPicker__body">
			<div class="iconPicker__icons">
				<a
					href="#"
					@click.stop.prevent="getIcon(icon)"
					:class="`item ${selected === icon ? 'selected' : ''}`"
					v-for="icon in icons"
					:key="icon"
				>
					<i :class="'fa '+icon"></i>
				</a>
			</div>
		</div>
	</div>
</template>

<script>
import icons from './icons';

export default {
    name: 'fontAwesomePicker',
    props: ['selected_icon', 'box_placeholder'],
	data () {
		return {
			selected: this.selected_icon,
			icons,
		};
	},
	methods: {
		getIcon (icon) {
			this.selected = icon;
			this.getContent(this.selected);
		},
		getContent (icon) {
			const iconContent = window
				.getComputedStyle(document.querySelector(`.fa.${icon}`), ':before')
				.getPropertyValue('content');
			this.convert(iconContent);
		},
		convert (value) {
			const newValue = value
				.charCodeAt(1)
				.toString(10)
				.replace(/\D/g, '');

			let hexValue = Number(newValue).toString(16);

			while (hexValue.length < 4) {
				hexValue = `0${hexValue}`;
			}

			this.selectIcon(hexValue.toUpperCase());
		},
		selectIcon (value) {
			const result = {
				className: this.selected,
				cssValue: value,
			};
			this.$emit('selectIcon', result);
		},
		filterIcons (event) {
			const search = event.target.value.trim();
			let filter = [];

            if(search.length == 0)
            {
                this.icons = icons;
            }
            else
            {
                filter = icons.filter((item) => {
					const regex = new RegExp(search, 'gi');
					return item.match(regex);
                });
                this.icons = filter;
            }
		},
	},
};
</script>

<style>
	#iconPicker {
		position: relative;
	}
	.iconPicker__header input {
		width: 100%;
		padding: 1em;
	}
	.iconPicker__body {
		position: relative;
		max-height: 200px;
		overflow: auto;
		padding: 1em 0 1em 1em;
		border-radius: 0 0 8px 8px;
		border: 1px solid #ccc;
	}
	.iconPicker__icons {
		display: table;
	}
	.iconPicker__icons .item {
		float: left;
	    width: 40px;
	    height: 40px;
	    padding: 12px;
	    margin: 0 12px 12px 0;
	    text-align: center;
	    border-radius: 3px;
	    font-size: 14px;
	    box-shadow: 0 0 0 1px #ddd;
	    color: inherit;
	}
	.iconPicker__icons .item.selected {
		background: #ccc;
	}
	.iconPicker__icons .item i {
		box-sizing: content-box;
	}
</style>
