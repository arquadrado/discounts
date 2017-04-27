export default {

	methods: {
		round (value) {
			return Number(value.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0])
			//return +(Math.round(value + "e+2")  + "e-2");
		}
	}
}