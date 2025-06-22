import { DirectoryDto } from "../../dtos/directory.dto";
import { FileDto } from "../../dtos/file.dto";
import { ApiResponse } from "../../contracts/api.response";
import { SearchStorageResponse } from "../../contracts/search-storage.response";

export interface StorageStateModel {
  currentDirectory?: DirectoryDto;
  directories: DirectoryDto[];
  files: FileDto[];
  searchResults?: SearchStorageResponse;
  searchQuery?: string;
  response?: ApiResponse;
}
