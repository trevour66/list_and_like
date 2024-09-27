const neonColors = [
	["green", "lime"], // Lime green and neon yellow-green
	["pink", "fuchsia"], // Neon pink and bold fuchsia
	["cyan", "blue"], // Neon cyan and electric blue
	["yellow", "amber"], // Neon yellow and vibrant amber
	["purple", "indigo"], // Neon violet and bright indigo
	["orange", "rose"], // Neon orange and neon rose red
	["red", "pink"], // Vibrant neon red and neon pink
	["lime", "cyan"], // Neon yellow-green and neon cyan
	["blue", "purple"], // Electric blue and neon violet
	["fuchsia", "rose"], // Bold fuchsia and neon rose red
	["green", "yellow"], // Bright lime green and neon yellow
	["orange", "red"], // Neon orange and vibrant neon red
	["indigo", "pink"], // Bright neon indigo and neon pink
	["cyan", "purple"], // Neon cyan and neon violet
	["amber", "fuchsia"], // Vibrant amber and bold fuchsia
];

const randomColor = () => {
	const randomColorValue =
		neonColors[Math.floor(Math.random() * neonColors.length)];

	return randomColorValue ?? neonColors[10];
};

export { neonColors, randomColor };
