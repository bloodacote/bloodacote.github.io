


async function loadProjectList() {
	let projPath = "/data.json";
	let projData = await loadJSON(projPath);

	for (let proj of projData) {
		let elemList = toElem("list");

		let elemProj = addElem("tab", elemList);
		elemProj.innerHTML = `
			<img class="prev" src="${proj.prevSource}">
			<text>
				<name> ${proj.name} </name>
				<desc> ${proj.desc} </desc>
			</text>
		`;

		elemProj.onclick = function() {
			window.open(proj.link, "_blank");
		};
	}

	console.log(projData);
}