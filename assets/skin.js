import "./styles/skin.css"
import "./js/skin_composer"
import "./js/global"
import {SkinViewer} from "skinview3d";

let skinViewer = new SkinViewer({
    canvas: document.getElementById("actualSkin"),
    width: 300,
    height: 400,
    skin: window.user.skinURL
});

console.log(window.user.skinURL);
skinViewer.loadSkin(window.user.skinURL).then(r => console.log(r));