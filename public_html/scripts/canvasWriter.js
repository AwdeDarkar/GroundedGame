world_map = [ [80, 5, "Awesomeness"], [54, 35, "Fun"], [22, 21, "Unowned"] ];
SCALE = 4;

for (var i = 0; i < world_map.length; i++)
{
	labelPoint(ctx, world_map[i][0], world_map[i][1], world_map[i][2]);
}

function labelPoint(ctx, xpos, ypos, label)
{
	ctx.beginPath();
	ctx.arc(xpos*SCALE, ypos*SCALE, 4, 0, 2*Math.PI);
	ctx.fill()
	ctx.fillText(label, xpos*SCALE + 6, ypos*SCALE);
}