import { Component, Input } from "@angular/core";

@Component({
  selector: "app-file-type-icon",
  imports: [],
  templateUrl: "./file-type-icon.component.html",
  styleUrl: "./file-type-icon.component.css",
})
export class FileTypeIconComponent {
  @Input({ required: true })
  public fileName: string = "";

  private readonly supportedBootstrapFiles = new Set([
    "doc",
    "docx",
    "pdf",
    "ppt",
    "pptx",
    "xls",
    "xlsx",
    "txt",
    "key",
    "md",
    "php",
    "py",
    "java",
    "sh",
    "sql",
    "rb",
    "json",
    "jsx",
    "yml",
    "mov",
    "mp3",
    "mp4",
    "wav",
    "aac",
    "exe",
    "otf",
    "mdx",
    "m4p",
    "ai",
    "svg",
    "woff",
    "tiff",
    "tsx",
    "ttf",
    "cs",
    "sass",
    "scss",
    "raw",
    "psd",
    "heic",
    "bmp",
    "gif",
    "jpg",
    "png",
    "html",
    "js",
    "css",
  ]);
  private readonly archiveTypes = new Set(["rar", "zip", "7z", "tar", "gz"]);
  private readonly defaultIcon: string = "bi-file-earmark";

  protected getBootstrapIconClass() {
    const fileType = this.getFileType();
    if (fileType === null) {
      return this.defaultIcon;
    }

    if (this.supportedBootstrapFiles.has(fileType)) {
      return `bi-filetype-${fileType}`;
    }

    if (this.archiveTypes.has(fileType)) {
      return "bi-file-earmark-zip";
    }

    return this.defaultIcon;
  }

  private getFileType() {
    const splitName = this.fileName.split(".");
    if (splitName.length === 0) {
      return null;
    }

    return splitName[splitName.length - 1].toLowerCase();
  }
}
