import { Injectable, Signal } from "@angular/core";
import { FileDto } from "../dtos/file.dto";
import { Store } from "@ngxs/store";
import { toSignalSync } from "../helpers/to-signal.helper";
import { CreateFile, DeleteFile } from "../states/file/file.actions";
import { FileState } from "../states/file/file.state";
import { ApiResponse } from "../contracts/api.response";

@Injectable({ providedIn: "root" })
export class FileController {
  public readonly files: Signal<FileDto[]>;
  public readonly response: Signal<ApiResponse | undefined>;

  constructor(private readonly store: Store) {
    this.files = toSignalSync(this.store.select(FileState.getFiles));
    this.response = toSignalSync(this.store.select(FileState.getResponse));
  }

  public createFile(file: File) {
    this.store.dispatch(new CreateFile(file));
  }

  public deleteFile(id: string) {
    this.store.dispatch(new DeleteFile(id));
  }
}
