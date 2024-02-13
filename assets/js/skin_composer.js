import {IdleAnimation, SkinViewer, WalkingAnimation} from "skinview3d";
import {async} from "regenerator-runtime";
import Pickr from "@simonwep/pickr";
import Sortable from "sortablejs";
import {eachLimit} from "async";
import Color from "colorjs.io";

function SkinComposer(elem) {
    this.container = elem;
    this.skinViewer = null;
    this.skinContainer = null;
    this.layout = {};
    this.parts = [];
    this.canvas = null;
    this.canvasViewer = null;

    this.init();
}

SkinComposer.prototype.initSkinViewer = function() {
    this.canvasViewer = document.createElement("canvas");
    this.canvasViewer.width = this.skinContainer.offsetWidth;
    this.canvasViewer.height = this.canvasViewer.width  / 1.618;
    //this.canvasViewer.style.position = 'absolute';
    //this.canvasViewer.style.left = `-${this.canvasViewer.width/4}px`;
    //const div = document.createElement("div");
    //div.appendChild(this.canvasViewer);
    this.skinContainer.appendChild(this.canvasViewer);
    this.skinViewer = new SkinViewer({
        canvas: this.canvasViewer,
        width: this.canvasViewer.width,
        height: this.canvasViewer.height
    });
    this.skinViewer.animation = new WalkingAnimation();
};

SkinComposer.prototype.initLayout = function() {

    // Appending parts container
    const leftColumn = document.createElement("div");
    leftColumn.classList.add("left-column");

    this.layout.leftColumn = leftColumn;
    this.initPartsStore();
    this.container.appendChild(leftColumn);

    // Appending skin viewer
    const skinContainer = document.createElement("div");
    skinContainer.classList.add("central-column");
    this.skinContainer = skinContainer;
    this.container.appendChild(skinContainer);

    // Creating layers container
    const rightColumn = document.createElement("div");
    rightColumn.classList.add("right-column")
    const layersContainer = document.createElement("div");
    layersContainer.classList.add("layers-container");

    this.layout.rightColumn = rightColumn;
    this.layout.rightColumn.layersContainer = layersContainer;
    rightColumn.appendChild(layersContainer);
    this.container.appendChild(rightColumn);
    this.refreshLayers();
};

SkinComposer.prototype.initPartsStore = function() {
    const that = this;
    fetch("/api/skin/assets")
        .then(result => result.json())
        .then(json => {
            const list = that.createPartList(json, 'resources');
            this.layout.leftColumn.append(list);
        })
};

SkinComposer.prototype.createPartList = function(parts, base) {
    const that = this;

    const list = document.createElement("ul");
    Object.keys(parts).forEach(dir => {
        const files = parts[dir];

        if (typeof files === "string") {
            const uri = base + '/' + files;
            list.appendChild(that.createPartListElem(uri, files));
        } else {
            const parent = document.createElement("li");
            const label = document.createElement("span");
            label.innerText = dir;
            label.classList.add("directory-label");
            parent.appendChild(label);
            if (Array.isArray(files)) {
                const filesList = document.createElement("ul");
                files.forEach(file => {
                    const uri = base + '/' + dir + '/' + file;
                    filesList.appendChild(that.createPartListElem(uri, file));
                });
                parent.appendChild(filesList);

                label.addEventListener('click', () => {
                    switch (filesList.style.display) {
                        case "block":
                            filesList.style.display = "none";
                            break;
                        default:
                            filesList.style.display = "block";
                            break;
                    }
                });
            } else if (typeof files === "object") {
                const sublist = that.createPartList(files, base + '/' + dir);
                parent.appendChild(sublist);
                label.addEventListener('click', () => {
                    switch (sublist.style.display) {
                        case "block":
                            sublist.style.display = "none";
                            break;
                        default:
                            sublist.style.display = "block";
                            break;
                    }
                });
            }
            list.appendChild(parent);
        }
    });
    return list;
};

SkinComposer.prototype.createPartListElem = function (uri, name) {
    const that = this;
    const item = document.createElement("li");
    item.classList.add("file");
    item.setAttribute("data-uri", uri);
    const img = new Image();
    const label = document.createElement("span")
    label.innerText = name;
    img.src = uri;
    item.appendChild(img);
    item.appendChild(label);

    const add = document.createElement("a");
    add.innerText = "+";
    add.classList.add("add-part")

    add.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        that.addPart(uri, name);
    });

    item.appendChild(add);

    return item;
}

SkinComposer.prototype.refreshLayers = function() {
    const that = this;
    that.layout.rightColumn.layersContainer.innerHTML = "";
    const layersList = document.createElement("ul")
    new Sortable(layersList, {
        onUpdate: function(e) {
            that.refreshPartsOrder();
        }
    });

    const sorted = JSON.parse(JSON.stringify(this.parts)).reverse();

    sorted.forEach(part => {
        const partWrapper = document.createElement("li");
        partWrapper.setAttribute('data-uri', part.uri);
        partWrapper.setAttribute('data-name', part.name);

        // Image
        const img = new Image();
        img.src = part.uri;
        img.width = 96;
        partWrapper.appendChild(img);

        const infoWrapper = document.createElement("div");
        infoWrapper.classList.add("part-infos");

        // Label
        const label = document.createElement("span");
        label.classList.add("choosed-part-label");
        label.innerText = part.name;
        infoWrapper.appendChild(label);

        // Color picker
        const color = document.createElement("div");
        infoWrapper.appendChild(color);

        const pickr = Pickr.create({
            el: color,
            theme: 'monolith', // or 'monolith', or 'nano',
            default: part.color || null,
            defaultRepresentation: 'RGBA',
            components: {
                // Main components
                preview: true,
                opacity: false,
                hue: true,
                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        });

        pickr.on('save', function(color) {
            if (color) partWrapper.setAttribute('data-color', color.toHEXA().toString());
            else partWrapper.removeAttribute('data-color');
            that.refreshPartsOrder();
        });

        pickr.on('clear', function() {
            partWrapper.removeAttribute('data-color');
            that.refreshPartsOrder();
        });

        partWrapper.pickr = pickr;

        if (part.color) {
            pickr.setColor(part.color);
            partWrapper.setAttribute('data-color', part.color);
        }

        // Delete button
        const deleteLink = document.createElement("a");
        deleteLink.classList.add("delete-part");
        deleteLink.innerText = "Delete";
        deleteLink.addEventListener("click", function(e) {
            e.preventDefault();
            that.deletePart(partWrapper);
        });
        infoWrapper.appendChild(deleteLink);

        // Appending
        layersList.appendChild(partWrapper);
        partWrapper.appendChild(infoWrapper);
    });

    that.layout.rightColumn.layersContainer.appendChild(layersList);
    this.drawCanvas().then(r => console.log(r));
};

SkinComposer.prototype.deletePart = function(partWrapper) {
    const that = this;
    this.parts.forEach((part, index) => {
        if (part.uri === partWrapper.getAttribute('data-uri')) {
            this.parts.slice(index, 1);
            partWrapper.pickr.destroyAndRemove();
            partWrapper.remove();
            that.refreshPartsOrder();
            that.refreshLayers();
        }
    });
};

SkinComposer.prototype.addPart = function(uri, name) {
    if (!this.hasPart(uri)) {
        this.parts.push({uri: uri, name: name});
        this.refreshLayers();
    }
}

SkinComposer.prototype.hasPart = function(uri) {
    let flag = false;
    this.parts.forEach((part) => {
        if (part.uri === uri) {
            flag = true;
        }
    });
    return flag;
};

SkinComposer.prototype.refreshPartsOrder = function() {
    const parts = [];
    this.layout.rightColumn.layersContainer.querySelectorAll("li").forEach(function(e) {
        const part = {
            uri: e.getAttribute('data-uri'),
            name: e.getAttribute('data-name')
        };

        if (e.getAttribute('data-color')) {
            part.color = e.getAttribute('data-color');
        }

        parts.push(part);
    });
    this.parts = parts.reverse();
    this.drawCanvas().then(result => console.log(result));
};

SkinComposer.prototype.init = function() {
    const that = this;

    this.initLayout();
    this.canvas = document.createElement("CANVAS");
    this.canvasContext = this.canvas.getContext('2d');
    this.initSkinViewer();

    const download = document.createElement("a");
    download.classList.add("download");
    download.innerText = "Télécharger";
    download.innerHTML += '<i class="fa-solid fa-download icon"></i>';
    download.addEventListener("click", function(e) {
        e.preventDefault();
        that.downloadImage();
    });
    const upload = document.createElement("a");
    upload.classList.add("confirm");
    upload.innerText = "Sauvegarder";
    upload.innerHTML += "<i class=\"fa-solid fa-floppy-disk icon\"></i>";
    upload.addEventListener("click", (e) => {
        e.preventDefault();
        that.uploadImage(that);
    });
    this.skinContainer.appendChild(download);
    this.skinContainer.appendChild(upload);
};

SkinComposer.prototype.computeCanvasSize = function() {
    return new Promise((resolve) => {
        const dim = {w: 0, h: 0};

        eachLimit(this.parts, 1, (part, next) => {
            const img = new Image();
            img.src = part.uri;

            img.onload = () => {
                if (img.width > dim.w) dim.w = img.width;
                if (img.height > dim.h) dim.h = img.height;
                next();
            };
        }, () => {
            resolve(dim);
        });
    });
};
SkinComposer.prototype.drawCanvas = async function() {
    const that = this;

    if (!this.parts.length) return;

    const d = await this.computeCanvasSize();
    this.canvas.width = d.w;
    this.canvas.height = d.h;

    eachLimit(this.parts, 1, (part, next) => {
        const img = new Image();
        img.src = part.uri;
        img.onload = () => {
            if (part.color) {
                const localCanvas = document.createElement("canvas");
                localCanvas.width = d.w;
                localCanvas.height = d.h;

                const localContext = localCanvas.getContext("2d");
                localContext.drawImage(img, 0, 0, d.w, d.h);

                const partColor = new Color(part.color).to("srgb");
                const data = localContext.getImageData(0,0, d.w, d.h);
                for(let i=0; i < data.data.length; i+=4) {
                    const r = data.data[i];
                    const g = data.data[i+1];
                    const b = data.data[i+2];

                    if (r || g || b ) {
                        const c = new Color(`rgb(${r}, ${g}, ${b})`).to('hsl');
                        if(c.s < 10) {
                            data.data[i] = partColor.r * 100;
                            data.data[i + 1] = partColor.g * 100;
                            data.data[i + 2] = partColor.b * 100;
                        }
                    }

                }
                localContext.putImageData(data, 0, 0);
                const tImg = new Image();
                tImg.src = localCanvas.toDataURL("image/png");

                tImg.onload = () => {
                    that.canvas.getContext("2d").drawImage(tImg, 0, 0, d.w, d.h);
                    next();
                };
            } else {
                const img = new Image();
                img.src = part.uri

                img.onload = () => {
                    console.log(this.canvasContext);
                    that.canvasContext.drawImage(img, 0, 0, d.w, d.h);
                    next();
                };
            }
        };
    }, () => {
        that.skinViewer.loadSkin(that.canvas.toDataURL("image/png"));
    });
};

SkinComposer.prototype.uploadImage = async (that) => {
    console.log(that.canvas);
    that.canvas.toBlob((blob) => {
        const file = new File([blob], `${window.user.username}.png`, { type: "image/png" })
        const formData = new FormData();
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById("skin_upload_skinImage_file").files = dataTransfer.files;
        document.getElementById("upload_skin").click();
    }, 'image/jpeg');
}

SkinComposer.prototype.downloadImage = function() {
    const link = document.createElement("a");
    link.href = this.canvas.toDataURL("image/png");
    link.download = "skin.png";
    document.body.append(link);
    link.click();
    link.remove();
};


export default SkinComposer;