export default {

	methods: {
		round (value) {
			
			return +(Math.round(value + "e+2")  + "e-2");
		}
	}
}