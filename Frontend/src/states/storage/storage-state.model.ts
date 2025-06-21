import { DirectoryDto } from "../../dtos/directory.dto";
import { FileDto } from "../../dtos/file.dto";
import { ApiResponse } from "../../contracts/api.response";

export interface StorageStateModel {
  currentDirectory?: DirectoryDto;
  directories: DirectoryDto[];
  files: FileDto[];
  response?: ApiResponse;
}
