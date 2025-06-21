import { Injectable, Signal } from "@angular/core";
import { FileDto } from "../dtos/file.dto";
import { Store } from "@ngxs/store";
import { toSignalSync } from "../helpers/to-signal.helper";
import { CreateFile, DeleteFile } from "../states/storage/file/file.actions";
import { FileService } from "../services/file.service";
import { StorageState } from "../states/storage/storage.state";

@Injectable({ providedIn: "root" })
export class FileController {
  public readonly files: Signal<FileDto[]>;

  constructor(
    private readonly store: Store,
    private readonly fileService: FileService
  ) {
    this.files = toSignalSync(this.store.select(StorageState.getFiles));
  }

  public async downloadFileViaAnchor(file: FileDto, anchor: HTMLAnchorElement) {
    const blob = await this.fileService.downloadFile(file.id);
    const url = window.URL.createObjectURL(blob);
    anchor.href = url;
    anchor.download = file.name;
    anchor.click();

    window.URL.revokeObjectURL(url);
  }

  public createFile(file: File) {
    this.store.dispatch(new CreateFile(file));
  }

  public deleteFile(id: string) {
    this.store.dispatch(new DeleteFile(id));
  }
}
