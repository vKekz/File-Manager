import { DirectoryDto } from "../dtos/directory.dto";
import { FileDto } from "../dtos/file.dto";

export interface SearchStorageResponse {
  directories: DirectoryDto[];
  files: FileDto[];
}
